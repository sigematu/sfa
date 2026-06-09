<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddRoleToClientContacts extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_contacts')) {
            return;
        }

        $table = $this->table('client_contacts');

        if (!$table->hasColumn('role')) {
            $table->addColumn('role', 'integer', [
                'null' => true,
                'after' => 'position',
            ]);
        }

        $table->update();
    }

    public function down(): void
    {
        if (!$this->hasTable('client_contacts')) {
            return;
        }

        $table = $this->table('client_contacts');

        if ($table->hasColumn('role')) {
            $table->removeColumn('role');
        }

        $table->update();
    }
}
