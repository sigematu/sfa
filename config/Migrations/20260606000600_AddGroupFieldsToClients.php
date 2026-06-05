<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddGroupFieldsToClients extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('clients')) {
            return;
        }

        $table = $this->table('clients');

        if (!$table->hasColumn('is_group')) {
            $table->addColumn('is_group', 'boolean', [
                'default' => 0,
                'null' => false,
                'after' => 'url',
            ]);
        }

        if (!$table->hasColumn('parent_id')) {
            $table->addColumn('parent_id', 'integer', [
                'null' => true,
                'after' => 'is_group',
            ]);
        }

        $table->update();
    }

    public function down(): void
    {
        if (!$this->hasTable('clients')) {
            return;
        }

        $table = $this->table('clients');

        if ($table->hasColumn('parent_id')) {
            $table->removeColumn('parent_id');
        }

        if ($table->hasColumn('is_group')) {
            $table->removeColumn('is_group');
        }

        $table->update();
    }
}
