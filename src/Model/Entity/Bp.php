<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Bp Entity
 *
 * @property int $id
 * @property string $name
 * @property string $kana
 * @property int|null $location
 * @property string|null $categories
 * @property string $created_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 */
class Bp extends Entity
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
        'name' => true,
        'kana' => true,
        'url' => true,
        'invoice_number' => true,
        'location' => true,
        'categories' => true,
        'note' => true,
        'mail_email_1' => true,
        'mail_dept_1' => true,
        'mail_flag_1' => true,
        'mail_email_2' => true,
        'mail_dept_2' => true,
        'mail_flag_2' => true,
        'mail_email_3' => true,
        'mail_dept_3' => true,
        'mail_flag_3' => true,
        'status' => true,
        'created' => true,
        'created_id' => true,
        'modified' => true,
        'modified_id' => true,
        'user' => true
    ];
}
