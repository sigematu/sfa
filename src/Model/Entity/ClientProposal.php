<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class ClientProposal extends Entity
{
    protected $_accessible = [
        'message_uid' => true,
        'received_at' => true,
        'sender' => true,
        'recipient' => true,
        'subject' => true,
        'body_text' => true,
        'body_html' => true,
        'headers' => true,
        'created' => true,
        'modified' => true,
    ];
}
