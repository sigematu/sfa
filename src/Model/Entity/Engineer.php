<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Engineer Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $type
 * @property string $name
 * @property string $kana
 * @property string $birthyear
 * @property string $year_industory_exp
 * @property string $skill_exp
 * @property string $year_skill_exp
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Contract[] $contracts
 */
class Engineer extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'user_id' => true,
        'emp_no' => true,
        'belong' => true,
        'name' => true,
        'kana' => true,
        'birthyear' => true,
        'year_industory_exp' => true,
        'skill_exp' => true,
        'year_skill_exp' => true,
        'skill_sheet' => true,
        'note' => true,
        'status' => true,
        'created' => true,
        'created_id' => true,
        'modified' => true,
        'modified_id' => true,
        'user' => true,
        'contracts' => true
    ];
}
