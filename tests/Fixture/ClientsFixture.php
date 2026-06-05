<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ClientsFixture
 */
class ClientsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array<string, mixed>
     */
    public $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'autoIncrement' => true],
        'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 255],
        'kana' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 255],
        'url' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255],
        'sales_rank' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 4],
        'note' => ['type' => 'text', 'null' => true, 'default' => null],
        'status' => ['type' => 'integer', 'null' => false, 'default' => 1],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'created_id' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50],
        'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'modified_id' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
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
                'id' => 1,
                'created_id' => '00000000-0000-0000-0000-000000000001',
                'name' => 'Lorem ipsum dolor sit amet',
                'kana' => 'Lorem ipsum dolor sit amet',
                'url' => null,
                'sales_rank' => null,
                'note' => null,
                'status' => 1,
                'created' => '2025-02-27 03:14:45',
                'modified' => '2025-02-27 03:14:45',
                'modified_id' => null,
            ],
        ];
        parent::init();
    }
}
