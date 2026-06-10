<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Clients Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Client newEmptyEntity()
 * @method \App\Model\Entity\Client newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Client[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Client get($primaryKey, $options = [])
 * @method \App\Model\Entity\Client findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Client patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Client[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Client|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Client saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Client[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Client[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Client[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Client[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ClientsTable extends Table
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

        $this->setTable('clients');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'created_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ClientContacts', [
        'foreignKey' => 'client_id',
        ]);

        $this->addBehavior("Search.Search");
        $this->searchManager()
            ->value('sales_rank')
            ->value('status')
            ->add('group_name', 'Search.Like', [
                'before' => true,
                'after' => true,
                'comparison' => 'LIKE',
                'fieldMode' => 'OR',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'fields' => ['group_name'],
            ])
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
            ->scalar('created_id')
            ->maxLength('created_id', 50)
            ->requirePresence('created_id', 'create')
            ->notEmptyString('created_id', __('This field is required.'));

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
            ->allowEmptyString('kana')
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
            ->integer('sales_rank')
            ->allowEmptyString('sales_rank');

        $validator
            ->scalar('group_name')
            ->maxLength('group_name', 255, __('Group name must be less than 255 characters.'))
            ->allowEmptyString('group_name')
            ->add('group_name', 'custom', [
                'rule' => 'noSpaceStartEnd',
                'provider' => 'custom',
                'message' => __('Spaces at the beginning or end, or full-width spaces are not allowed.')
            ]);

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

        for ($i = 1; $i <= 5; $i++) {
            $validator
                ->scalar("mail_email_{$i}")
                ->maxLength("mail_email_{$i}", 255, __('Email must be less than 255 characters.'))
                ->email("mail_email_{$i}", false, __('Email format is invalid.'))
                ->allowEmptyString("mail_email_{$i}");

            $validator
                ->scalar("mail_dept_{$i}")
                ->maxLength("mail_dept_{$i}", 255, __('Department or role must be less than 255 characters.'))
                ->allowEmptyString("mail_dept_{$i}");

            $validator
                ->integer("mail_flag_{$i}")
                ->allowEmptyString("mail_flag_{$i}");
        }

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
        $rules->add($rules->existsIn('created_id', 'Users'), ['errorField' => 'created_id']);
        $rules->add($rules->isUnique(['name'], __('This name is already in use.')));

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
