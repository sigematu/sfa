<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddAreaOnlyDeliveryToClientContacts extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_contacts')) {
            return;
        }

        $table = $this->table('client_contacts');
        if (!$table->hasColumn('area_only_delivery')) {
            $table
                ->addColumn('area_only_delivery', 'integer', [
                    'null' => false,
                    'default' => 0,
                    'after' => 'mail_delivery',
                ])
                ->update();
        }
    }

    public function down(): void
    {
        if (!$this->hasTable('client_contacts')) {
            return;
        }

        $table = $this->table('client_contacts');
        if ($table->hasColumn('area_only_delivery')) {
            $table
                ->removeColumn('area_only_delivery')
                ->update();
        }
    }
}
