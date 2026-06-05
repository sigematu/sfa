<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array<string, mixed>
     */
    public $fields = [
        'id' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 36],
        'username' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 255],
        'email' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 255],
        'password' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 255],
        'first_name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
        'last_name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
        'display_name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
        'job' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
        'position' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
        'token' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
        'token_expires' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'api_token' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
        'activation_date' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'secret' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
        'secret_verified' => ['type' => 'boolean', 'null' => true, 'default' => 0],
        'tos_date' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'active' => ['type' => 'boolean', 'null' => false, 'default' => 1],
        'is_superuser' => ['type' => 'boolean', 'null' => false, 'default' => 0],
        'role' => ['type' => 'string', 'null' => true, 'default' => 'user', 'length' => 255],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'additional_data' => ['type' => 'text', 'null' => true, 'default' => null],
        'last_login' => ['type' => 'datetime', 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => '00000000-0000-0000-0000-000000000001',
                'username' => 'test-user',
                'email' => 'test@example.com',
                'password' => '$2y$10$wH6Q4QyS2cf6U8mqq9fW3OGSxQq9f3w1HcQ2kN6uQJmXG1Jz6f9kK',
                'active' => 1,
                'is_superuser' => 0,
                'role' => 'user',
                'created' => '2025-02-27 02:23:01',
                'modified' => '2025-02-27 02:23:01',
            ],
        ];
        parent::init();
    }
}
