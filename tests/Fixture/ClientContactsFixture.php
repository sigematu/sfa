<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ClientContactsFixture
 */
class ClientContactsFixture extends TestFixture
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
                'client_id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'kana' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet',
                'mobile_phone' => 'Lorem ipsum dolor sit amet',
                'landline_phone' => 'Lorem ipsum dolor sit amet',
                'department' => 'Lorem ipsum dolor sit amet',
                'position' => 1,
                'category' => 1,
                'hierarchy' => null,
                'note' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'status' => 1,
                'inactive_reason' => null,
                'mail_delivery' => 0,
                'area_only_delivery' => 0,
                'created' => '2026-04-15 23:36:47',
                'created_id' => '',
                'modified' => '2026-04-15 23:36:47',
                'modified_id' => '',
            ],
        ];
        parent::init();
    }
}
