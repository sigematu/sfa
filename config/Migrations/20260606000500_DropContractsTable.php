<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class DropContractsTable extends AbstractMigration
{
    public function up(): void
    {
        if ($this->hasTable('contracts')) {
            $this->table('contracts')->drop()->save();
        }
    }

    public function down(): void
    {
        if (!$this->hasTable('contracts')) {
            $this->table('contracts')
                ->addColumn('client_id', 'integer', ['null' => false])
                ->addColumn('created', 'datetime', ['null' => true])
                ->addColumn('modified', 'datetime', ['null' => true])
                ->create();
        }
    }
}
