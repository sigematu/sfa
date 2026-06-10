<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AlterClientBusinessDevelopmentsStatusToText extends AbstractMigration
{
    public function change(): void
    {
        $this->table('client_business_developments')
            ->changeColumn('status', 'text', ['null' => true, 'default' => null])
            ->update();
    }
}
