<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddLocationAndBaseToClientContacts extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_contacts')) {
            return;
        }

        $table = $this->table('client_contacts');
        if (!$table->hasColumn('location')) {
            $table->addColumn('location', 'integer', [
                'null' => true,
                'default' => null,
                'after' => 'hierarchy',
            ]);
        }
        if (!$table->hasColumn('base')) {
            $table->addColumn('base', 'string', [
                'limit' => 255,
                'null' => true,
                'default' => null,
                'after' => 'location',
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
        if ($table->hasColumn('base')) {
            $table->removeColumn('base');
        }
        if ($table->hasColumn('location')) {
            $table->removeColumn('location');
        }
        $table->update();
    }
}
