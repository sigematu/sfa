<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ClientBusinessDevelopmentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('client_business_developments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);

        $this->belongsTo('Clients', [
            'foreignKey' => 'client_id',
        ]);

        $this->belongsTo('ClientContacts', [
            'foreignKey' => 'client_contact_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->dateTime('action_at')
            ->requirePresence('action_at', 'create')
            ->notEmptyDateTime('action_at');

        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        $validator
            ->integer('client_id')
            ->requirePresence('client_id', 'create')
            ->notEmptyString('client_id');

        $validator
            ->integer('client_contact_id')
            ->allowEmptyString('client_contact_id');

        $validator
            ->integer('sales_status')
            ->requirePresence('sales_status', 'create')
            ->notEmptyString('sales_status')
            ->add('sales_status', 'inList', [
                'rule' => ['inList', array_keys(CLIENT_BIZ_DEV_SALES_STATUS_LABELS)],
                'message' => __('Invalid sales status.'),
            ]);

        $validator
            ->integer('sales_reason')
            ->allowEmptyString('sales_reason')
            ->add('sales_reason', 'inList', [
                'rule' => ['inList', array_keys(CLIENT_BIZ_DEV_REASON_LABELS)],
                'message' => __('Invalid sales reason.'),
            ]);

        $validator
            ->scalar('status')
            ->allowEmptyString('status')
            ->maxLength('status', 2000);

        return $validator;
    }
}
