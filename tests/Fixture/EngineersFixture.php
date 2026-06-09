<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EngineersFixture
 */
class EngineersFixture extends TestFixture
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
                'type' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'kana' => 'Lorem ipsum dolor sit amet',
                'birthyear' => 'Lorem ipsum dolor sit amet',
                'year_industory_exp' => 'Lorem ipsum dolor sit amet',
                'skill_exp' => 'Lorem ipsum dolor sit amet',
                'year_skill_exp' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-02-27 05:08:52',
                'modified' => '2025-02-27 05:08:52',
            ],
        ];
        parent::init();
    }
}
