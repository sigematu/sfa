<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddEvaluationToClientProposals extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_proposals')) {
            return;
        }

        $table = $this->table('client_proposals');
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
        if (!$this->hasTable('client_proposals')) {
            return;
        }

        $table = $this->table('client_proposals');
        if ($table->hasColumn('evaluation')) {
            $table
                ->removeColumn('evaluation')
                ->update();
        }
    }
}
