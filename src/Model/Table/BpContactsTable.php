<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * BpContacts Model
 *
 * @property \App\Model\Table\BpsTable&\Cake\ORM\Association\BelongsTo $Bps
 *
 * @method \App\Model\Entity\BpContact newEmptyEntity()
 * @method \App\Model\Entity\BpContact newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\BpContact[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BpContact get($primaryKey, $options = [])
 * @method \App\Model\Entity\BpContact findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\BpContact patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BpContact[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\BpContact|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BpContact saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BpContact[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\BpContact[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\BpContact[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\BpContact[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BpContactsTable extends Table
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

        $this->setTable('bp_contacts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Bps', [
            'foreignKey' => 'bp_id',
            'joinType' => 'INNER',
        ]);

        $this->addBehavior("Search.Search");
        $this->searchManager()
            ->value('bp_id')
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
            ->integer('bp_id')
            ->notEmptyString('bp_id');

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
        $rules->add($rules->existsIn('bp_id', 'Bps'), ['errorField' => 'bp_id']);

        return $rules;
    }
}
