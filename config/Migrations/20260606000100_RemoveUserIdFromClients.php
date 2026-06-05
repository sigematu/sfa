<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveUserIdFromClients extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('clients')) {
            return;
        }

        $this->execute("UPDATE clients SET created_id = user_id WHERE (created_id IS NULL OR created_id = '') AND user_id IS NOT NULL");

        $table = $this->table('clients');
        if ($table->hasColumn('user_id')) {
            $table
                ->removeColumn('user_id')
                ->update();
        }
    }

    public function down(): void
    {
        if (!$this->hasTable('clients')) {
            return;
        }

        $table = $this->table('clients');
        if (!$table->hasColumn('user_id')) {
            $table
                ->addColumn('user_id', 'string', [
                    'limit' => 50,
                    'null' => false,
                    'default' => '',
                    'after' => 'id',
                ])
                ->update();
        }

        $this->execute("UPDATE clients SET user_id = created_id WHERE created_id IS NOT NULL AND created_id <> ''");
    }
}
