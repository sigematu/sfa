<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ClientContact Entity
 *
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property string $kana
 * @property string $email
 * @property string|null $mobile_phone
 * @property string|null $landline_phone
 * @property string|null $department
 * @property string|null $position_title
 * @property int|null $position
 * @property int|null $category
 * @property int|null $role
 * @property int|null $hierarchy
 * @property int|null $location
 * @property string|null $base
 * @property string|null $note
 * @property int $status
 * @property int|null $inactive_reason
 * @property int $mail_delivery
 * @property int $area_only_delivery
 * @property \Cake\I18n\FrozenTime $created
 * @property string $created_id
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $modified_id
 *
 * @property \App\Model\Entity\Client $client
 */
class ClientContact extends Entity
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
        'client_id' => true,
        'name' => true,
        'kana' => true,
        'email' => true,
        'mobile_phone' => true,
        'landline_phone' => true,
        'department' => true,
        'position_title' => true,
        'position' => true,
        'category' => true,
        'role' => true,
        'hierarchy' => true,
        'location' => true,
        'base' => true,
        'note' => true,
        'status' => true,
        'inactive_reason' => true,
        'mail_delivery' => true,
        'area_only_delivery' => true,
        'created' => true,
        'created_id' => true,
        'modified' => true,
        'modified_id' => true,
        'client' => true,
    ];
}
