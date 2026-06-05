<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddInactiveReasonToClientContacts extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_contacts')) {
            return;
        }

        $table = $this->table('client_contacts');
        if (!$table->hasColumn('inactive_reason')) {
            $table
                ->addColumn('inactive_reason', 'integer', [
                    'null' => true,
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
        if ($table->hasColumn('inactive_reason')) {
            $table
                ->removeColumn('inactive_reason')
                ->update();
        }
    }
}
