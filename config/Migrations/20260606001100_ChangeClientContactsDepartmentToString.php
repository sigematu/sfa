<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class ChangeClientContactsDepartmentToString extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_contacts')) {
            return;
        }

        $table = $this->table('client_contacts');
        if ($table->hasColumn('department')) {
            $table
                ->changeColumn('department', 'string', [
                    'limit' => 255,
                    'null' => true,
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
        if ($table->hasColumn('department')) {
            $table
                ->changeColumn('department', 'integer', [
                    'null' => true,
                ])
                ->update();
        }
    }
}
