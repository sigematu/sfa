<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddEvaluationToBpProcurements extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('bp_procurements')) {
            return;
        }

        $table = $this->table('bp_procurements');
        if (!$table->hasColumn('evaluation')) {
            $table
                ->addColumn('evaluation', 'integer', [
                    'null' => false,
                    'default' => 0,
                    'after' => 'sales_reason',
                ])
                ->update();
        }
    }

    public function down(): void
    {
        if (!$this->hasTable('bp_procurements')) {
            return;
        }

        $table = $this->table('bp_procurements');
        if ($table->hasColumn('evaluation')) {
            $table
                ->removeColumn('evaluation')
                ->update();
        }
    }
}
