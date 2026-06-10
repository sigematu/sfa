<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Table\BpProcurementsTable;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

class Pop3BpProcurementService
{
    private const STRING_LIMIT_255 = 255;
    private const STRING_LIMIT_500 = 500;
    private const TEXT_LIMIT_BYTES = 65535;

    private BpProcurementsTable $bpProcurements;

    /** @var array<string, mixed> */
    private array $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(BpProcurementsTable $bpProcurements, array $config = [])
    {
        $this->bpProcurements = $bpProcurements;
        $this->config = $config;
    }

    public function import(): int
    {
        if (!function_exists('imap_open')) {
            throw new \RuntimeException(__('POP3 extension (imap) is not enabled.'));
        }

        $host = (string)($this->config['host'] ?? '');
        $port = (int)($this->config['port'] ?? 995);
        $username = (string)($this->config['username'] ?? '');
        $password = (string)($this->config['password'] ?? '');
        $ssl = (bool)($this->config['ssl'] ?? true);
        $novalidateCert = (bool)($this->config['novalidate_cert'] ?? true);
        $maxMessages = (int)($this->config['max_messages'] ?? 100);

        if ($host === '' || $username === '' || $password === '') {
            throw new \RuntimeException(__('POP3 settings are not configured.'));
        }

        $mailbox = sprintf(
            '{%s:%d/pop3%s%s}INBOX',
            $host,
            $port,
            $ssl ? '/ssl' : '',
            $novalidateCert ? '/novalidate-cert' : ''
        );

        $inbox = @imap_open($mailbox, $username, $password);
        if ($inbox === false) {
            throw new \RuntimeException(__('Failed to connect to POP3 server.'));
        }

        try {
            $uids = imap_search($inbox, 'ALL', SE_UID);
            if (empty($uids)) {
                $this->refreshConnectionForSessionWrite();

                return 0;
            }

            rsort($uids);
            $uids = array_slice($uids, 0, $maxMessages);

            $imported = 0;
            foreach ($uids as $uid) {
                $uidString = (string)$uid;

                $exists = (int)$this->runWithReconnect(function () use ($uidString): int {
                    return $this->bpProcurements->find()
                        ->where(['message_uid' => $uidString])
                        ->count();
                });
                if ($exists > 0) {
                    continue;
                }

                $msgNo = imap_msgno($inbox, (int)$uid);
                if ($msgNo <= 0) {
                    continue;
                }

                $overviewList = imap_fetch_overview($inbox, (string)$uid, FT_UID);
                $overview = $overviewList[0] ?? null;

                $subject = $this->decodeMime($overview->subject ?? '');
                $sender = $this->decodeMime($overview->from ?? '');
                $senderUserId = $this->resolveSenderUserId($sender);
                $recipient = $this->decodeMime($overview->to ?? '');
                $receivedAt = !empty($overview->date)
                    ? new FrozenTime((string)$overview->date)
                    : null;

                $bodyText = trim((string)imap_fetchbody($inbox, $msgNo, '1'));
                if ($bodyText === '') {
                    $bodyText = trim((string)imap_body($inbox, $msgNo));
                }

                $bodyHtml = trim((string)imap_fetchbody($inbox, $msgNo, '2'));
                $headers = (string)imap_fetchheader($inbox, $msgNo);
                $resolvedRecipient = $this->resolveRecipientForImport($subject, $recipient, $bodyText, $bodyHtml);

                $entity = $this->bpProcurements->newEntity([
                    'message_uid' => $uidString,
                    'received_at' => $receivedAt,
                    'sender' => $senderUserId !== null ? (string)$senderUserId : '',
                    'recipient' => $this->truncateByChars($resolvedRecipient, self::STRING_LIMIT_255),
                    'subject' => $this->truncateByChars($subject, self::STRING_LIMIT_500),
                    'sales_status' => BP_PROCUREMENT_STATUS_PROCURING,
                    'sales_reason' => BP_PROCUREMENT_REASON_UNSET,
                    'body_text' => $this->truncateByBytes($bodyText, self::TEXT_LIMIT_BYTES),
                    'body_html' => $this->truncateByBytes($bodyHtml, self::TEXT_LIMIT_BYTES),
                    'headers' => $this->truncateByBytes($headers, self::TEXT_LIMIT_BYTES),
                ]);

                if ($this->runWithReconnect(function () use ($entity): bool {
                    return (bool)$this->bpProcurements->save($entity);
                })) {
                    $imported++;
                }
            }

            $this->refreshConnectionForSessionWrite();

            return $imported;
        } finally {
            imap_close($inbox);
        }
    }

    /**
     * @template T
     * @param callable():T $callback
     * @return T
     */
    private function runWithReconnect(callable $callback)
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            if (!$this->isGoneAwayError($e)) {
                throw $e;
            }

            $this->reconnectDatabase();

            return $callback();
        }
    }

    private function refreshConnectionForSessionWrite(): void
    {
        $this->runWithReconnect(function (): void {
            $this->bpProcurements->getConnection()->execute('SELECT 1');
        });
    }

    private function reconnectDatabase(): void
    {
        $connection = $this->bpProcurements->getConnection();
        $connection->getDriver()->disconnect();
        $connection->execute('SELECT 1');
    }

    private function isGoneAwayError(\Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        return strpos($message, 'server has gone away') !== false
            || strpos($message, 'error while sending query packet') !== false
            || strpos($message, 'lost connection to mysql server') !== false;
    }

    private function decodeMime(string $value): string
    {
        $decoded = imap_mime_header_decode($value);
        $parts = [];
        foreach ($decoded as $part) {
            $text = (string)($part->text ?? '');
            $charset = strtoupper((string)($part->charset ?? 'UTF-8'));
            if ($charset !== 'DEFAULT' && $charset !== 'UTF-8') {
                $converted = @mb_convert_encoding($text, 'UTF-8', $charset);
                $parts[] = $converted !== false ? $converted : $text;
            } else {
                $parts[] = $text;
            }
        }

        return trim(implode('', $parts));
    }

    private function truncateByChars(string $value, int $limit): string
    {
        if ($value === '') {
            return '';
        }

        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit);
    }

    private function truncateByBytes(string $value, int $limit): string
    {
        if ($value === '') {
            return '';
        }

        if (strlen($value) <= $limit) {
            return $value;
        }

        return mb_strcut($value, 0, $limit, 'UTF-8');
    }

    private function resolveRecipientForImport(string $subject, string $defaultRecipient, string $bodyText, string $bodyHtml): string
    {
        if (!$this->isForwardSubject($subject)) {
            return $defaultRecipient;
        }

        $forwardedRecipient = $this->extractForwardedRecipient($bodyText);
        if ($forwardedRecipient === '' && $bodyHtml !== '') {
            $forwardedRecipient = $this->extractForwardedRecipient(strip_tags($bodyHtml));
        }

        return $forwardedRecipient !== '' ? $forwardedRecipient : $defaultRecipient;
    }

    private function isForwardSubject(string $subject): bool
    {
        return (bool)preg_match('/^\s*(?:fwd?|fw|転送)\s*[:：]/iu', $subject);
    }

    private function extractForwardedRecipient(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $normalized = str_replace(["\r\n", "\r"], "\n", $text);
        foreach (explode("\n", $normalized) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (preg_match('/^(?:to|宛先)\s*[:：]\s*(.+)$/iu', $line, $matches)) {
                return trim((string)$matches[1]);
            }
        }

        return '';
    }

    private function resolveSenderUserId(string $sender): ?int
    {
        if ($sender === '') {
            return null;
        }

        if (!preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $sender, $matches)) {
            return null;
        }

        $email = strtolower((string)$matches[0]);
        if ($email === '') {
            return null;
        }

        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->find()
            ->select(['id'])
            ->where(['LOWER(email) =' => $email])
            ->first();

        return $user ? (int)$user->id : null;
    }
}
