<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateClientBusinessDevelopments extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('client_business_developments');
        $table
            ->addColumn('action_at', 'datetime', ['null' => true])
            ->addColumn('user_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('client_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('client_contact_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('sales_status', 'integer', ['null' => true, 'default' => null])
            ->addColumn('sales_reason', 'integer', ['null' => true, 'default' => null])
            ->addColumn('status', 'integer', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['action_at'])
            ->addIndex(['user_id'])
            ->addIndex(['client_id'])
            ->create();
    }
}
