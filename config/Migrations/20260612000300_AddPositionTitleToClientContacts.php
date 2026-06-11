<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddPositionTitleToClientContacts extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_contacts')) {
            return;
        }

        $table = $this->table('client_contacts');
        if (!$table->hasColumn('position_title')) {
            $table
                ->addColumn('position_title', 'string', [
                    'limit' => 255,
                    'null' => true,
                    'default' => null,
                    'after' => 'department',
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
        if ($table->hasColumn('position_title')) {
            $table
                ->removeColumn('position_title')
                ->update();
        }
    }
}
