<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddCategoriesToBps extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('bps')) {
            return;
        }

        $table = $this->table('bps');
        if (!$table->hasColumn('categories')) {
            $table
                ->addColumn('categories', 'text', [
                    'null' => true,
                    'after' => 'location',
                ])
                ->update();
        }
    }

    public function down(): void
    {
        if (!$this->hasTable('bps')) {
            return;
        }

        $table = $this->table('bps');
        if ($table->hasColumn('categories')) {
            $table
                ->removeColumn('categories')
                ->update();
        }
    }
}
