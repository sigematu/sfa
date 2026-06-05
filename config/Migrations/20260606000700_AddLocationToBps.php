<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddLocationToBps extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('bps')) {
            return;
        }

        $table = $this->table('bps');
        if (!$table->hasColumn('location')) {
            $table
                ->addColumn('location', 'integer', [
                    'null' => true,
                    'after' => 'invoice_number',
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
        if ($table->hasColumn('location')) {
            $table
                ->removeColumn('location')
                ->update();
        }
    }
}
