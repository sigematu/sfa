<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateMissingCoreTablesForTests extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('users')) {
            $this->table('users', ['id' => false, 'primary_key' => ['id']])
                ->addColumn('id', 'uuid', ['null' => false])
                ->addColumn('username', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('password', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('first_name', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('last_name', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('display_name', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('job', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('position', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('token', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('token_expires', 'datetime', ['null' => true])
                ->addColumn('api_token', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('activation_date', 'datetime', ['null' => true])
                ->addColumn('secret', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('secret_verified', 'boolean', ['null' => true, 'default' => 0])
                ->addColumn('tos_date', 'datetime', ['null' => true])
                ->addColumn('active', 'boolean', ['null' => false, 'default' => 1])
                ->addColumn('is_superuser', 'boolean', ['null' => false, 'default' => 0])
                ->addColumn('role', 'string', ['limit' => 255, 'null' => true, 'default' => 'user'])
                ->addColumn('created', 'datetime', ['null' => false])
                ->addColumn('modified', 'datetime', ['null' => true])
                ->addColumn('additional_data', 'text', ['null' => true])
                ->addColumn('last_login', 'datetime', ['null' => true])
                ->create();
        }

        if (!$this->hasTable('clients')) {
            $this->table('clients')
                ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('kana', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('url', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('sales_rank', 'integer', ['null' => true])
                ->addColumn('note', 'text', ['null' => true])
                ->addColumn('status', 'integer', ['null' => false, 'default' => 1])
                ->addColumn('created', 'datetime', ['null' => false])
                ->addColumn('created_id', 'char', ['limit' => 50, 'null' => false])
                ->addColumn('modified', 'datetime', ['null' => true])
                ->addColumn('modified_id', 'char', ['limit' => 50, 'null' => true])
                ->create();
        }

        if (!$this->hasTable('bps')) {
            $this->table('bps')
                ->addColumn('created_id', 'char', ['limit' => 50, 'null' => false])
                ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('kana', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('location', 'integer', ['null' => true])
                ->addColumn('categories', 'text', ['null' => true])
                ->addColumn('fee', 'integer', ['null' => false])
                ->addColumn('created', 'datetime', ['null' => true])
                ->addColumn('modified', 'datetime', ['null' => true])
                ->create();
        }

        if (!$this->hasTable('engineers')) {
            $this->table('engineers')
                ->addColumn('user_id', 'integer', ['null' => false])
                ->addColumn('type', 'integer', ['null' => false])
                ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('kana', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('birthyear', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('year_industory_exp', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('skill_exp', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('year_skill_exp', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('created', 'datetime', ['null' => true])
                ->addColumn('modified', 'datetime', ['null' => true])
                ->create();
        }

        if (!$this->hasTable('client_contacts')) {
            $this->table('client_contacts')
                ->addColumn('client_id', 'integer', ['null' => false])
                ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('kana', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('email', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('mobile_phone', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('landline_phone', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('department', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('position', 'integer', ['null' => true])
                ->addColumn('category', 'integer', ['null' => true])
                ->addColumn('hierarchy', 'integer', ['null' => true])
                ->addColumn('note', 'text', ['null' => true])
                ->addColumn('status', 'integer', ['null' => false, 'default' => 1])
                ->addColumn('inactive_reason', 'integer', ['null' => true])
                ->addColumn('mail_delivery', 'integer', ['null' => false, 'default' => 0])
                ->addColumn('area_only_delivery', 'integer', ['null' => false, 'default' => 0])
                ->addColumn('created', 'datetime', ['null' => true])
                ->addColumn('created_id', 'char', ['limit' => 50, 'null' => true])
                ->addColumn('modified', 'datetime', ['null' => true])
                ->addColumn('modified_id', 'char', ['limit' => 50, 'null' => true])
                ->create();
        }
    }

    public function down(): void
    {
        // No-op: this migration is intentionally non-destructive.
    }
}
