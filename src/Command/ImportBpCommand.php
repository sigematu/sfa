<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\FactoryLocator;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use RuntimeException;

class ImportBpCommand extends Command
{
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription('Import BP and BP contacts from an Excel file.')
            ->addOption('file', [
                'short' => 'f',
                'help' => 'Path to xlsx file',
                'default' => ROOT . DS . 'resources' . DS . 'bp.xlsx',
            ])
            ->addOption('created-id', [
                'help' => 'created_id/modified_id used for created or updated records',
                'default' => '1',
            ])
            ->addOption('dry-run', [
                'boolean' => true,
                'help' => 'Validate and parse only. Do not persist any changes.',
                'default' => false,
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $file = (string)$args->getOption('file');
        if (!is_file($file) && preg_match('/\\.xksx$/i', $file)) {
            $file = preg_replace('/\\.xksx$/i', '.xlsx', $file) ?? $file;
        }
        if (!is_file($file)) {
            $io->err(sprintf('File not found: %s', $file));

            return static::CODE_ERROR;
        }

        $dryRun = (bool)$args->getOption('dry-run');
        $createdId = trim((string)$args->getOption('created-id'));
        $activeStatus = defined('STATUS_ACTIVE') ? (int)STATUS_ACTIVE : 1;

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $sheet = $reader->load($file)->getActiveSheet();
        $highestCol = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        $rows = $sheet->rangeToArray('A1:' . $highestCol . $highestRow, null, false, false, true);
        if (empty($rows[1])) {
            $io->err('Header row is empty.');

            return static::CODE_ERROR;
        }

        $headerMap = $this->buildHeaderMap($rows[1]);
        $requiredHeaders = [
            ['name' => 'BP', 'aliases' => ['BP', 'BP名', '会社名', '企業名']],
            ['name' => 'BP担当者', 'aliases' => ['BP担当者', '担当者', 'BP担当']],
            ['name' => 'メールアドレス', 'aliases' => ['メールアドレス', 'email', 'mail']],
        ];
        foreach ($requiredHeaders as $requiredHeader) {
            if ($this->findColumn($headerMap, $requiredHeader['aliases']) === null) {
                $io->err('Required header not found: ' . $requiredHeader['name']);

                return static::CODE_ERROR;
            }
        }

        $cols = [
            'bp_name' => $this->findColumn($headerMap, ['BP', 'BP名', '会社名', '企業名']),
            'bp_kana' => $this->findColumn($headerMap, ['BPカナ', 'BP名カナ', '会社名カナ', 'カナ']),
            'bp_url' => $this->findColumn($headerMap, ['URL', 'url', 'ホームページ', 'HP']),
            'bp_invoice' => $this->findColumn($headerMap, ['法人番号', '適格番号', '登録番号', 'インボイス番号', 'インボイス登録番号', 'invoice_number']),
            'bp_note' => $this->findColumn($headerMap, ['BP備考', '備考']),
            'contact_name' => $this->findColumn($headerMap, ['BP担当者', '担当者', 'BP担当']),
            'contact_kana' => $this->findColumn($headerMap, ['BP担当者カナ', '担当者カナ']),
            'contact_email' => $this->findColumn($headerMap, ['メールアドレス', 'email', 'mail']),
            'contact_mobile' => $this->findColumn($headerMap, ['携帯番号', '携帯電話']),
            'contact_landline' => $this->findColumn($headerMap, ['固定電話', '電話番号', 'TEL']),
            'contact_position' => $this->findColumn($headerMap, ['役職']),
            'contact_note' => $this->findColumn($headerMap, ['BP担当者備考', '担当者備考']),
        ];

        $bpsTable = FactoryLocator::get('Table')->get('Bps');
        $bpContactsTable = FactoryLocator::get('Table')->get('BpContacts');

        $stats = [
            'bp_created' => 0,
            'bp_updated' => 0,
            'bp_skipped' => 0,
            'contact_created' => 0,
            'contact_updated' => 0,
            'contact_skipped' => 0,
        ];
        $invoiceSkipWarnings = 0;

        $bpInvoiceMap = [];
        $bpNameMap = [];
        foreach ($bpsTable->find()->select(['id', 'name', 'invoice_number'])->all() as $existingBp) {
            $existingInvoice = $this->digits((string)$existingBp->invoice_number);
            if (strlen($existingInvoice) === 13) {
                $bpInvoiceMap[$existingInvoice] = $existingBp;
            }
            $bpNameMap[$this->normalizeName((string)$existingBp->name)] = $existingBp;
        }
        /** @var \Cake\Database\Connection $conn */
        $conn = ConnectionManager::get('default');
        $conn->begin();
        try {
            for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
                $row = $rows[$rowNum] ?? [];
                $bpName = $this->value($row, $cols['bp_name']);
                $contactName = $this->value($row, $cols['contact_name']);
                $contactEmail = $this->value($row, $cols['contact_email']);
                $invoiceNumber = $this->digits($this->value($row, $cols['bp_invoice']));

                if ($bpName === '') {
                    $stats['bp_skipped']++;
                    $stats['contact_skipped']++;
                    continue;
                }

                $normalizedBpName = $this->normalizeName($bpName);
                $bp = null;
                if (strlen($invoiceNumber) === 13 && isset($bpInvoiceMap[$invoiceNumber])) {
                    $bp = $bpInvoiceMap[$invoiceNumber];
                } elseif (isset($bpNameMap[$normalizedBpName])) {
                    $bp = $bpNameMap[$normalizedBpName];
                }
                $isNewBp = $bp === null;
                if ($isNewBp) {
                    if (strlen($invoiceNumber) !== 13) {
                        $stats['bp_skipped']++;
                        $stats['contact_skipped']++;
                        if ($invoiceSkipWarnings < 20) {
                            $io->warning(sprintf('Row %d skipped: invalid or missing invoice number for new BP "%s".', $rowNum, $bpName));
                        }
                        $invoiceSkipWarnings++;
                        continue;
                    }

                    $bp = $bpsTable->newEntity([
                        'name' => $bpName,
                        'kana' => $this->fallback($this->value($row, $cols['bp_kana']), $bpName),
                        'url' => $this->value($row, $cols['bp_url']),
                        'invoice_number' => $invoiceNumber,
                        'note' => $this->value($row, $cols['bp_note']),
                        'status' => $activeStatus,
                        'created_id' => $createdId,
                    ]);
                } else {
                    $patch = [
                        'modified_id' => $createdId,
                    ];
                    $bpKana = $this->value($row, $cols['bp_kana']);
                    if ($bpKana !== '') {
                        $patch['kana'] = $bpKana;
                    }
                    $bpUrl = $this->value($row, $cols['bp_url']);
                    if ($bpUrl !== '') {
                        $patch['url'] = $bpUrl;
                    }
                    if (strlen($invoiceNumber) === 13) {
                        $patch['invoice_number'] = $invoiceNumber;
                    }
                    $bpNote = $this->value($row, $cols['bp_note']);
                    if ($bpNote !== '') {
                        $patch['note'] = $bpNote;
                    }
                    $bp = $bpsTable->patchEntity($bp, $patch);
                }

                if (!$bpsTable->save($bp)) {
                    throw new RuntimeException(sprintf('Failed saving BP at row %d: %s', $rowNum, json_encode($bp->getErrors(), JSON_UNESCAPED_UNICODE)));
                }

                if (strlen((string)$bp->invoice_number) > 0) {
                    $bpInvoiceMap[$this->digits((string)$bp->invoice_number)] = $bp;
                }
                $bpNameMap[$this->normalizeName((string)$bp->name)] = $bp;

                if ($isNewBp) {
                    $stats['bp_created']++;
                } else {
                    $stats['bp_updated']++;
                }

                if ($contactName === '' || $contactEmail === '') {
                    $stats['contact_skipped']++;
                    continue;
                }

                $contact = $bpContactsTable
                    ->find()
                    ->where(['bp_id' => $bp->id, 'email' => $contactEmail])
                    ->first();
                $isNewContact = $contact === null;

                $contactData = [
                    'bp_id' => $bp->id,
                    'name' => $contactName,
                    'kana' => $this->fallback($this->value($row, $cols['contact_kana']), $contactName),
                    'email' => $contactEmail,
                    'mobile_phone' => $this->normalizeMobilePhone($this->value($row, $cols['contact_mobile'])),
                    'landline_phone' => $this->normalizeLandlinePhone($this->value($row, $cols['contact_landline'])),
                    'position' => $this->value($row, $cols['contact_position']),
                    'note' => $this->value($row, $cols['contact_note']),
                    'status' => $activeStatus,
                ];

                if ($isNewContact) {
                    $contactData['created_id'] = $createdId;
                    $contact = $bpContactsTable->newEntity($contactData);
                } else {
                    $contactData['modified_id'] = $createdId;
                    $contact = $bpContactsTable->patchEntity($contact, $contactData);
                }

                if (!$bpContactsTable->save($contact)) {
                    throw new RuntimeException(sprintf('Failed saving BP contact at row %d: %s', $rowNum, json_encode($contact->getErrors(), JSON_UNESCAPED_UNICODE)));
                }

                if ($isNewContact) {
                    $stats['contact_created']++;
                } else {
                    $stats['contact_updated']++;
                }
            }

            if ($dryRun) {
                $conn->rollback();
                $io->out('Dry-run completed. No changes were committed.');
            } else {
                $conn->commit();
                $io->success('Import completed.');
            }
        } catch (\Throwable $e) {
            $conn->rollback();
            $io->err($e->getMessage());

            return static::CODE_ERROR;
        }

        if ($invoiceSkipWarnings > 20) {
            $io->out(sprintf('invoice_skip_warnings_suppressed=%d', $invoiceSkipWarnings - 20));
        }

        $io->out(sprintf('bp_created=%d bp_updated=%d bp_skipped=%d', $stats['bp_created'], $stats['bp_updated'], $stats['bp_skipped']));
        $io->out(sprintf('contact_created=%d contact_updated=%d contact_skipped=%d', $stats['contact_created'], $stats['contact_updated'], $stats['contact_skipped']));

        return static::CODE_SUCCESS;
    }

    /**
     * @param array<string, mixed> $headerRow
     * @return array<string, string>
     */
    private function buildHeaderMap(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $col => $name) {
            $normalized = $this->normalizeHeader((string)$name);
            if ($normalized !== '') {
                $map[$normalized] = (string)$col;
            }
        }

        return $map;
    }

    /**
     * @param array<string, string> $headerMap
     * @param array<int, string> $aliases
     */
    private function findColumn(array $headerMap, array $aliases): ?string
    {
        foreach ($aliases as $alias) {
            $key = $this->normalizeHeader($alias);
            if (isset($headerMap[$key])) {
                return $headerMap[$key];
            }
        }

        return null;
    }

    private function normalizeHeader(string $header): string
    {
        $normalized = trim(mb_strtolower($header));
        $normalized = str_replace([' ', '　'], '', $normalized);

        return $normalized;
    }

    private function normalizeName(string $name): string
    {
        return $this->normalizeHeader($name);
    }

    private function digits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    private function normalizeMobilePhone(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        if (preg_match('/^0[789]0-\d{4}-\d{4}$/', $trimmed)) {
            return $trimmed;
        }

        $digits = $this->digits($trimmed);
        if (strlen($digits) === 11 && preg_match('/^0[789]0\d{8}$/', $digits)) {
            return substr($digits, 0, 3) . '-' . substr($digits, 3, 4) . '-' . substr($digits, 7, 4);
        }

        return '';
    }

    private function normalizeLandlinePhone(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        if (preg_match('/^0\d{1,4}-\d{1,4}-\d{4}$/', $trimmed)) {
            return $trimmed;
        }

        $digits = $this->digits($trimmed);
        if (strlen($digits) === 10) {
            if (str_starts_with($digits, '03') || str_starts_with($digits, '06')) {
                return substr($digits, 0, 2) . '-' . substr($digits, 2, 4) . '-' . substr($digits, 6, 4);
            }

            return substr($digits, 0, 3) . '-' . substr($digits, 3, 3) . '-' . substr($digits, 6, 4);
        }

        if (strlen($digits) === 11) {
            return substr($digits, 0, 3) . '-' . substr($digits, 3, 4) . '-' . substr($digits, 7, 4);
        }

        return '';
    }

    /**
     * @param array<string, mixed> $row
     */
    private function value(array $row, ?string $col): string
    {
        if ($col === null) {
            return '';
        }

        return trim((string)($row[$col] ?? ''));
    }

    private function fallback(string $value, string $fallback): string
    {
        if ($value !== '') {
            return $value;
        }

        return $fallback;
    }
}
