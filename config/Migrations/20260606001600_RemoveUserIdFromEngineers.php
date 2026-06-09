<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveUserIdFromEngineers extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('engineers')) {
            return;
        }

        $table = $this->table('engineers');

        if ($table->hasColumn('user_id')) {
            $this->execute("UPDATE engineers SET created_id = user_id WHERE (created_id IS NULL OR created_id = '') AND user_id IS NOT NULL");
            $table
                ->removeColumn('user_id')
                ->update();
        }
    }

    public function down(): void
    {
        if (!$this->hasTable('engineers')) {
            return;
        }

        $table = $this->table('engineers');

        if (!$table->hasColumn('user_id')) {
            $table
                ->addColumn('user_id', 'integer', [
                    'null' => true,
                    'after' => 'id',
                ])
                ->update();
        }

        $this->execute("UPDATE engineers SET user_id = created_id WHERE created_id IS NOT NULL AND created_id <> ''");
    }
}