<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddBpPicIdToClientProposals extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_proposals')) {
            return;
        }

        $table = $this->table('client_proposals');
        if (!$table->hasColumn('bp_pic_id')) {
            $table
                ->addColumn('bp_pic_id', 'integer', [
                    'null' => true,
                    'default' => null,
                    'after' => 'sender',
                ])
                ->update();
        }
    }

    public function down(): void
    {
        if (!$this->hasTable('client_proposals')) {
            return;
        }

        $table = $this->table('client_proposals');
        if ($table->hasColumn('bp_pic_id')) {
            $table
                ->removeColumn('bp_pic_id')
                ->update();
        }
    }
}
