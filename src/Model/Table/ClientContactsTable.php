<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClientContacts Model
 *
 * @property \App\Model\Table\ClientsTable&\Cake\ORM\Association\BelongsTo $Clients
 *
 * @method \App\Model\Entity\ClientContact newEmptyEntity()
 * @method \App\Model\Entity\ClientContact newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ClientContact[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClientContact get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClientContact findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ClientContact patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClientContact[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClientContact|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClientContact saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClientContact[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ClientContact[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ClientContact[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ClientContact[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ClientContactsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('client_contacts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Clients', [
            'foreignKey' => 'client_id',
            'joinType' => 'INNER',
        ]);

        $this->addBehavior("Search.Search");
        $this->searchManager()
            ->value('client_id')
            ->value('position')
            ->value('status')
            ->add('q', 'Search.Like', [
                'before' => true,
                'after' => true,
                'fieldMode' => 'OR',
                'comparison' => 'LIKE',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'fields' => ['name', 'kana'],
            ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->setProvider('custom', 'App\Model\Validation\CustomValidator');

        $validator
            ->integer('client_id')
            ->notEmptyString('client_id');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->add('name', 'custom', [
                'rule' => 'noSpaceStartEnd',
                'provider' => 'custom',
                'message' => __('Spaces at the beginning or end, or full-width spaces are not allowed.')
            ]);

        $validator
            ->scalar('kana')
            ->maxLength('kana', 255)
            ->requirePresence('kana', 'create')
            ->notEmptyString('kana')
            ->add('kana', 'custom', [
                'rule' => 'noSpaceStartEnd',
                'provider' => 'custom',
                'message' => __('Spaces at the beginning or end, or full-width spaces are not allowed.')
            ]);

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('mobile_phone')
            ->maxLength('mobile_phone', 50)
            ->add('mobile_phone', 'custom', [
                'rule' => 'isMobilePhoneNumber',
                'provider' => 'custom',
                'message' => __('Invalid mobile phone number format (e.g. 090-0000-0000).')
            ])
            ->allowEmptyString('mobile_phone');

        $validator
            ->scalar('landline_phone')
            ->maxLength('landline_phone', 50)
            ->add('landline_phone', 'custom', [
                'rule' => 'isLandlinePhoneNumber',
                'provider' => 'custom',
                'message' => __('Invalid landline phone number format (e.g. 03-0000-0000).')
            ])
            ->allowEmptyString('landline_phone');

        $validator
            ->scalar('position')
            ->maxLength('position', 255)
            ->allowEmptyString('position');

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        $validator
            ->scalar('created_id')
            ->maxLength('created_id', 50)
            ->requirePresence('created_id', 'create')
            ->notEmptyString('created_id');

        $validator
            ->scalar('modified_id')
            ->maxLength('modified_id', 50)
            ->allowEmptyString('modified_id');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('Client_id', 'Clients'), ['errorField' => 'Client_id']);

        return $rules;
    }
}
