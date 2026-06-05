<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class ClearKanaInContacts extends AbstractMigration
{
    public function up(): void
    {
        if ($this->hasTable('client_contacts') && $this->table('client_contacts')->hasColumn('kana')) {
            $this->execute('UPDATE client_contacts SET kana = NULL WHERE kana IS NOT NULL AND kana <> ""');
        }

        if ($this->hasTable('bp_contacts') && $this->table('bp_contacts')->hasColumn('kana')) {
            $this->execute('UPDATE bp_contacts SET kana = NULL WHERE kana IS NOT NULL AND kana <> ""');
        }
    }

    public function down(): void
    {
        // Data reset migration: no down operation.
    }
}
