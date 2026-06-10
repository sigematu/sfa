<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateBpProcurements extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('bp_procurements');
        $table
            ->addColumn('message_uid', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('received_at', 'datetime', ['null' => true])
            ->addColumn('sender', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('recipient', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('subject', 'string', ['limit' => 500, 'null' => true])
            ->addColumn('sales_status', 'integer', ['null' => true, 'default' => null])
            ->addColumn('sales_reason', 'integer', ['null' => true, 'default' => null])
            ->addColumn('body_text', 'text', ['null' => true])
            ->addColumn('body_html', 'text', ['null' => true])
            ->addColumn('headers', 'text', ['null' => true])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['message_uid'], ['unique' => true])
            ->addIndex(['received_at'])
            ->create();
    }
}
