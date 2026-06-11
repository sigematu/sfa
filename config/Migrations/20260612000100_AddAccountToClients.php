<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAccountToClients extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('clients')) {
            return;
        }

        $table = $this->table('clients');
        if (!$table->hasColumn('account')) {
            $table
                ->addColumn('account', 'boolean', [
                    'default' => false,
                    'null' => false,
                    'after' => 'sales_rank',
                ])
                ->update();
        }
    }

    public function down(): void
    {
        if (!$this->hasTable('clients')) {
            return;
        }

        $table = $this->table('clients');
        if ($table->hasColumn('account')) {
            $table
                ->removeColumn('account')
                ->update();
        }
    }
}
