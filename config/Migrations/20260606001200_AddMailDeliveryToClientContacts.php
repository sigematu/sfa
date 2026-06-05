<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddMailDeliveryToClientContacts extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_contacts')) {
            return;
        }

        $table = $this->table('client_contacts');
        if (!$table->hasColumn('mail_delivery')) {
            $table
                ->addColumn('mail_delivery', 'integer', [
                    'null' => false,
                    'default' => 0,
                    'after' => 'status',
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
        if ($table->hasColumn('mail_delivery')) {
            $table
                ->removeColumn('mail_delivery')
                ->update();
        }
    }
}
