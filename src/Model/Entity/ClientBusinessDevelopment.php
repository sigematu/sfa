<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class ClientBusinessDevelopment extends Entity
{
    protected $_accessible = [
        'action_at' => true,
        'user_id' => true,
        'client_id' => true,
        'client_contact_id' => true,
        'sales_status' => true,
        'sales_reason' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
    ];
}
