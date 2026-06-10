<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class BpProcurementsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('bp_procurements');
        $this->setDisplayField('subject');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('message_uid')
            ->maxLength('message_uid', 255)
            ->allowEmptyString('message_uid');

        $validator
            ->dateTime('received_at')
            ->allowEmptyDateTime('received_at');

        $validator
            ->scalar('sender')
            ->maxLength('sender', 255)
            ->allowEmptyString('sender');

        $validator
            ->scalar('recipient')
            ->maxLength('recipient', 255)
            ->allowEmptyString('recipient');

        $validator
            ->scalar('subject')
            ->maxLength('subject', 500)
            ->allowEmptyString('subject');

        $validator
            ->integer('sales_status')
            ->allowEmptyString('sales_status')
            ->add('sales_status', 'inList', [
                'rule' => ['inList', array_keys(BP_PROCUREMENT_STATUS_LABELS)],
                'message' => __('Invalid sales status.'),
            ]);

        $validator
            ->integer('sales_reason')
            ->allowEmptyString('sales_reason')
            ->add('sales_reason', 'inList', [
                'rule' => ['inList', array_keys(BP_PROCUREMENT_REASON_LABELS)],
                'message' => __('Invalid sales reason.'),
            ]);

        $validator
            ->scalar('body_text')
            ->allowEmptyString('body_text');

        $validator
            ->scalar('body_html')
            ->allowEmptyString('body_html');

        $validator
            ->scalar('headers')
            ->allowEmptyString('headers');

        return $validator;
    }
}
