<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddMailRecipientsToBps extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('bps')) {
            return;
        }

        $table = $this->table('bps');
        $after = 'note';
        for ($i = 1; $i <= 3; $i++) {
            if (!$table->hasColumn("mail_email_{$i}")) {
                $table->addColumn("mail_email_{$i}", 'string', [
                    'limit' => 255,
                    'null' => true,
                    'default' => null,
                    'after' => $after,
                ]);
            }
            if (!$table->hasColumn("mail_dept_{$i}")) {
                $table->addColumn("mail_dept_{$i}", 'string', [
                    'limit' => 255,
                    'null' => true,
                    'default' => null,
                    'after' => "mail_email_{$i}",
                ]);
            }
            if (!$table->hasColumn("mail_flag_{$i}")) {
                $table->addColumn("mail_flag_{$i}", 'integer', [
                    'null' => false,
                    'default' => 0,
                    'after' => "mail_dept_{$i}",
                ]);
            }
            $after = "mail_flag_{$i}";
        }
        $table->update();
    }

    public function down(): void
    {
        if (!$this->hasTable('bps')) {
            return;
        }

        $table = $this->table('bps');
        for ($i = 1; $i <= 3; $i++) {
            foreach (["mail_email_{$i}", "mail_dept_{$i}", "mail_flag_{$i}"] as $column) {
                if ($table->hasColumn($column)) {
                    $table->removeColumn($column);
                }
            }
        }
        $table->update();
    }
}
