<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;

/**
 * Engineers Model
 *
 * @method \App\Model\Entity\Engineer newEmptyEntity()
 * @method \App\Model\Entity\Engineer newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Engineer[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Engineer get($primaryKey, $options = [])
 * @method \App\Model\Entity\Engineer findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Engineer patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Engineer[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Engineer|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Engineer saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Engineer[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Engineer[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Engineer[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Engineer[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EngineersTable extends Table
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

        $this->setTable('engineers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior("Search.Search");
        $this->searchManager()
            ->value('belong')
            ->callback('skill_sheet', [
                'callback' => function ($query, $args, $manager) {
                    if (isset($args['skill_sheet'])) {
                        if ($args['skill_sheet'] === SKILL_SHEET_UPLOADED) {
                            $query->where(['skill_sheet IS NOT' => null])->where(['skill_sheet !=' => '']);
                        } elseif ($args['skill_sheet'] === SKILL_SHEET_NOT_UPLOADED) {
                            $query->where(['OR' => [['skill_sheet IS' => null], ['skill_sheet' => '']]]);
                        }
                    }
                }
            ])
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
            ->integer('emp_no')
            ->allowEmptyString('emp_no');

        $validator
            ->integer('belong')
            ->requirePresence('belong', 'create')
            ->notEmptyString('belong', __('This field is required.'));

        $validator
            ->scalar('name')
            ->maxLength('name', 255, __('Name must be less than 255 characters.'))
            ->requirePresence('name', 'create')
            ->notEmptyString('name', __('This field is required.'))
            ->add('name', 'custom', [
                'rule' => 'noSpaceStartEnd',
                'provider' => 'custom',
                'message' => __('Spaces at the beginning or end, or full-width spaces are not allowed.')
            ]);

        $validator
            ->scalar('kana')
            ->maxLength('kana', 255, __('Name Kana must be less than 255 characters.'))
            ->requirePresence('kana', 'create')
            ->notEmptyString('kana', __('This field is required.'))
            ->add('kana', 'custom', [
                'rule' => 'noSpaceStartEnd',
                'provider' => 'custom',
                'message' => __('Spaces at the beginning or end, or full-width spaces are not allowed.')
            ]);

        $validator
            ->integer('birthyear', __('Birthyear must be an integer.'))
            ->add('birthyear', 'validValue', [
                'rule' => ['range', 1901, 2155],
                'message' => __('Birthyear needs to be four digits.')
            ])
            ->allowEmptyString('birthyear');

        $validator
            ->scalar('year_industory_exp')
            ->maxLength('year_industory_exp', 255, __('Industory Experience must be less than 255 characters.'))
            ->allowEmptyString('year_industory_exp')
            ->add('year_industory_exp', 'custom', [
                'rule' => 'noSpaceStartEnd',
                'provider' => 'custom',
                'message' => __('Spaces at the beginning or end, or full-width spaces are not allowed.')
            ]);

        $validator
            ->scalar('skill_exp')
            ->maxLength('skill_exp', 255, __('Skill must be less than 255 characters.'))
            ->allowEmptyString('skill_exp')
            ->add('skill_exp', 'custom', [
                'rule' => 'noSpaceStartEnd',
                'provider' => 'custom',
                'message' => __('Spaces at the beginning or end, or full-width spaces are not allowed.')
            ]);

        $validator
            ->scalar('year_skill_exp')
            ->maxLength('year_skill_exp', 255, __('Skill Experience must be less than 255 characters.'))
            ->allowEmptyString('year_skill_exp')
            ->add('year_skill_exp', 'custom', [
                'rule' => 'noSpaceStartEnd',
                'provider' => 'custom',
                'message' => __('Spaces at the beginning or end, or full-width spaces are not allowed.')
            ]);

        $validator
            ->allowEmptyDate('skill_sheet');

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmptyString('status', __('This field is required.'));

        return $validator;
    }

    // public function checkYear($value, $context)
    // {
    //     return (bool) preg_match('/^[0-9]{4}$/', $value);
    // }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['name'], __('This name is already in use.')));
        $rules->add($rules->isUnique(['emp_no'], __('This Employee ID is already in use.')));

        return $rules;
    }

    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->get('birthyear') === '') {
            $entity->set('birthyear', null);
        }
    }
}
