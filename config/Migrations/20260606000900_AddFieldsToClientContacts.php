<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddFieldsToClientContacts extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_contacts')) {
            return;
        }

        $table = $this->table('client_contacts');

        if (!$table->hasColumn('department')) {
            $table->addColumn('department', 'integer', [
                'null' => true,
                'after' => 'landline_phone',
            ]);
        }

        if (!$table->hasColumn('category')) {
            $table->addColumn('category', 'integer', [
                'null' => true,
                'after' => 'position',
            ]);
        }

        if (!$table->hasColumn('hierarchy')) {
            $table->addColumn('hierarchy', 'integer', [
                'null' => true,
                'after' => 'category',
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

        if ($table->hasColumn('hierarchy')) {
            $table->removeColumn('hierarchy');
        }

        if ($table->hasColumn('category')) {
            $table->removeColumn('category');
        }

        if ($table->hasColumn('department')) {
            $table->removeColumn('department');
        }

        $table->update();
    }
}
