<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BpsFixture
 */
class BpsFixture extends TestFixture
{
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
                'fee' => 1,
                'created' => '2025-02-27 03:14:48',
                'modified' => '2025-02-27 03:14:48',
            ],
        ];
        parent::init();
    }
}
