<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveUserIdFromBps extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('bps')) {
            return;
        }

        $table = $this->table('bps');

        if (!$table->hasColumn('created_id')) {
            $table
                ->addColumn('created_id', 'char', [
                    'limit' => 50,
                    'null' => true,
                    'after' => 'created',
                ])
                ->update();
        }

        if ($table->hasColumn('user_id')) {
            $this->execute("UPDATE bps SET created_id = user_id WHERE (created_id IS NULL OR created_id = '') AND user_id IS NOT NULL");
            $table
                ->removeColumn('user_id')
                ->update();
        }
    }

    public function down(): void
    {
        if (!$this->hasTable('bps')) {
            return;
        }

        $table = $this->table('bps');

        if (!$table->hasColumn('user_id')) {
            $table
                ->addColumn('user_id', 'char', [
                    'limit' => 50,
                    'null' => true,
                    'after' => 'id',
                ])
                ->update();
        }

        $this->execute("UPDATE bps SET user_id = created_id WHERE created_id IS NOT NULL AND created_id <> ''");
    }
}
