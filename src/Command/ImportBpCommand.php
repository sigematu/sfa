<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\BpImportService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class ImportBpCommand extends Command
{
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription('Import BP and BP contacts from a CSV file.')
            ->addOption('file', [
                'short' => 'f',
                'help' => 'Path to CSV file',
                'default' => ROOT . DS . 'resources' . DS . 'bp.csv',
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
        if (!is_file($file)) {
            $io->err(sprintf('File not found: %s', $file));

            return static::CODE_ERROR;
        }

        $dryRun = (bool)$args->getOption('dry-run');
        $createdId = trim((string)$args->getOption('created-id'));

        try {
            $stats = (new BpImportService())->import($file, $createdId, $dryRun);
        } catch (\Throwable $e) {
            $io->err($e->getMessage());

            return static::CODE_ERROR;
        }

        $warnings = (array)($stats['warnings'] ?? []);
        $shown = 0;
        foreach ($warnings as $warning) {
            if ($shown >= 20) {
                break;
            }
            $io->warning((string)$warning);
            $shown++;
        }
        if (count($warnings) > 20) {
            $io->out(sprintf('invoice_skip_warnings_suppressed=%d', count($warnings) - 20));
        }

        if ($dryRun) {
            $io->out('Dry-run completed. No changes were committed.');
        } else {
            $io->success('Import completed.');
        }

        $io->out(sprintf('bp_created=%d bp_updated=%d bp_skipped=%d', $stats['bp_created'], $stats['bp_updated'], $stats['bp_skipped']));
        $io->out(sprintf('contact_created=%d contact_updated=%d contact_skipped=%d', $stats['contact_created'], $stats['contact_updated'], $stats['contact_skipped']));

        return static::CODE_SUCCESS;
    }
}
