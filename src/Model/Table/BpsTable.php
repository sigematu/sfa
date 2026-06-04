<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Bps Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ContractsTable&\Cake\ORM\Association\HasMany $Contracts
 *
 * @method \App\Model\Entity\Bp newEmptyEntity()
 * @method \App\Model\Entity\Bp newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Bp[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Bp get($primaryKey, $options = [])
 * @method \App\Model\Entity\Bp findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Bp patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Bp[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Bp|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Bp saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Bp[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Bp[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Bp[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Bp[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BpsTable extends Table
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

        $this->setTable('bps');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('BpContacts', [
            'foreignKey' => 'bp_id',
        ]);

        $this->addBehavior("Search.Search");
        $this->searchManager()
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
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id', __('This field is required.'));

        $validator
            ->scalar('name')
            ->maxLength('name', 255, __('Company must be less than 255 characters.'))
            ->requirePresence('name', 'create')
            ->notEmptyString('name', __('This field is required.'))
            ->add('name', 'custom', [
                'rule' => 'noSpaceStartEnd',
                'provider' => 'custom',
                'message' => __('Spaces at the beginning or end, or full-width spaces are not allowed.')
            ]);

        $validator
            ->scalar('kana')
            ->maxLength('kana', 255, __('Company Kana must be less than 255 characters.'))
            ->requirePresence('kana', 'create')
            ->notEmptyString('kana', __('This field is required.'))
            ->add('kana', 'custom', [
                'rule' => 'noSpaceStartEnd',
                'provider' => 'custom',
                'message' => __('Spaces at the beginning or end, or full-width spaces are not allowed.')
            ]);

        $validator
            ->scalar('url')
            ->url('url', __('URL format is invalid.'))
            ->allowEmptyString('url');

        $validator
            ->integer('invoice_number', __('Invoice Number must be an integer.'))
            ->regex('invoice_number', '/^[0-9]{13}$/', __('Invoice Number must be entered as 13 digits.'))
            ->requirePresence('invoice_number', 'create')
            ->notEmptyString('invoice_number', __('This field is required.'));

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmptyString('status', __('This field is required.'));

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
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->isUnique(['name'], __('This name is already in use.')));
        $rules->add($rules->isUnique(['invoice_number'], __('Invoice Number is already in use.')));

        // urlが空の場合、重複チェックを外す
        $rules->add(
            function ($entity, $options) use ($rules) {
                if (empty($entity->url)) {
                    return true;
                }

                $rule = $rules->isUnique(['url'], __('This url is already in use.'));
                return $rule($entity, $options);
            }
        );

        return $rules;
    }
}
