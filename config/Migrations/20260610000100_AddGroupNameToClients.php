<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddGroupNameToClients extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('clients')) {
            return;
        }

        $table = $this->table('clients');
        if (!$table->hasColumn('group_name')) {
            $table
                ->addColumn('group_name', 'string', [
                    'limit' => 255,
                    'null' => true,
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
        if ($table->hasColumn('group_name')) {
            $table
                ->removeColumn('group_name')
                ->update();
        }
    }
}
