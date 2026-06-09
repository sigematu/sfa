<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddSalesFieldsToClientProposals extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('client_proposals')) {
            return;
        }

        $table = $this->table('client_proposals');
        $updated = false;

        if (!$table->hasColumn('sales_status')) {
            $table->addColumn('sales_status', 'integer', [
                'null' => true,
                'default' => null,
                'after' => 'subject',
            ]);
            $updated = true;
        }

        if (!$table->hasColumn('sales_reason')) {
            $table->addColumn('sales_reason', 'integer', [
                'null' => true,
                'default' => null,
                'after' => 'sales_status',
            ]);
            $updated = true;
        }

        if ($updated) {
            $table->update();
        }
    }

    public function down(): void
    {
        if (!$this->hasTable('client_proposals')) {
            return;
        }

        $table = $this->table('client_proposals');
        $updated = false;

        if ($table->hasColumn('sales_reason')) {
            $table->removeColumn('sales_reason');
            $updated = true;
        }

        if ($table->hasColumn('sales_status')) {
            $table->removeColumn('sales_status');
            $updated = true;
        }

        if ($updated) {
            $table->update();
        }
    }
}
