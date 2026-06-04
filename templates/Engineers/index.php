<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Engineer[]|\Cake\Collection\CollectionInterface $engineers
 */
?>
<?php
$this->assign('title', __('Engineers'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Engineers')],
]);

$colDefs = [
    'id'              => __('Id'),
    'actions'         => __('Actions'),
    'emp-no'          => __('Employee No.'),
    'name'            => __('Name'),
    'belong'          => __('Belong'),
    'birthyear'       => __('Birthyear'),
    'industry-exp'    => __('Year of Industry Experience'),
    'skill-exp'       => __('Skill Experience'),
    'skill-exp-years' => __('Year of Skill Experience'),
    'status'          => __('Status'),
];
$alwaysCols = ['actions', 'name'];
?>

<p>
  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseEngineer" aria-expanded="false" aria-controls="collapseEngineer">
    <?= __('Open Search') ?>
  </button>
</p>
<div class="collapse" id="collapseEngineer">
    <div class="card card-body">
        <div class="container">
            <div class="row">
                <div class="col">
                    <?php
                        echo $this->Form->create(null, ['valueSources' => 'query']);
                        echo $this->Form->control('q', ['label' => __('Name')]);
                        ?>
                </div>
                <div class="col">
                    <?php
                        $belongs = ['1' => __('Proper Staff'), '2' => __('Bp Staff')];
                        echo $this->Form->control('belong', ['type' => 'select', 'label' => __('Belong'), 'options' => $belongs, 'empty' => true]);
                    ?>
                </div>
                <div class="col">
                    <?php
                        $skill_sheet = [SKILL_SHEET_UPLOADED => __('Uploaded'), SKILL_SHEET_NOT_UPLOADED => __('Not Uploaded')];
                        echo $this->Form->control('skill_sheet', ['type' => 'select', 'label' => __('Skill Sheet'), 'options' => $skill_sheet, 'empty' => true]);
                    ?>
                </div>
                <div class="col">
                    <?php
                        $statuses = ['0' => __('Inactive'), '1' => __('Active')];
                        echo $this->Form->control('status', ['type' => 'select', 'label' => __('Status'), 'options' => $statuses, 'empty' => true]);
                    ?>
                </div>
                <div class="col align-self-center">
                    <?= $this->element('search'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header d-sm-flex">
        <h2 class="card-title">
            <!-- -->
        </h2>
        <div class="card-toolbox">
            <?= $this->element('col_toggle', ['colToggleKey' => 'engineers_hidden_cols', 'colDefs' => $colDefs, 'alwaysCols' => $alwaysCols]) ?>
            <?= $this->Paginator->limitControl([], null, [
                'label' => false,
                'class' => 'form-control-sm',
            ]); ?>
            <?= $this->Html->link(__('New'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped text-nowrap">
            <thead>
                <tr>
                    <th class="pc-id"><?= $this->Paginator->sort('id') ?></th>
                    <th class="pc-actions actions"><?= __('Actions') ?></th>
                    <th class="pc-emp-no"><?= $this->Paginator->sort('emp_no', ['label' => __('Employee No.')]) ?></th>
                    <th class="pc-name"><?= $this->Paginator->sort('kana', $title = __('Name')) ?></th>
                    <th class="pc-belong"><?= $this->Paginator->sort('belong') ?></th>
                    <th class="pc-birthyear"><?= $this->Paginator->sort('birthyear') ?></th>
                    <th class="pc-industry-exp"><?= $this->Paginator->sort('year_industory_exp', $title = __('Year of Industry Experience')) ?></th>
                    <th class="pc-skill-exp"><?= $this->Paginator->sort('skill_exp', $title = __('Skill Experience')) ?></th>
                    <th class="pc-skill-exp-years"><?= $this->Paginator->sort('year_skill_exp', $title = __('Year of Skill Experience')) ?></th>
                    <th class="pc-status"><?= $this->Paginator->sort('status') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($engineers as $engineer) : ?>
                    <tr>
                        <td class="pc-id"><?= $this->Number->format($engineer->id) ?></td>
                        <td class="pc-actions actions">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $engineer->id], ['class' => 'btn btn-xs btn-outline-primary', 'escape' => false]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $engineer->id], ['class' => 'btn btn-xs btn-outline-danger', 'escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $engineer->id)]) ?>
                        </td>
                        <td class="pc-emp-no"><?= h($engineer->emp_no) ?></td>
                        <td class="pc-name"><?= $this->Html->link(h($this->Text->truncate($engineer->name, 30)), ['action' => 'view', $engineer->id]) ?></td>
                        <td class="pc-belong">
                            <?php if ($engineer->belong === BELONG_OUR): ?>
                                <?= __('Proper Staff') ?>
                            <?php else: ?>
                                <?= __('Bp Staff') ?>
                            <?php endif; ?>
                        </td>
                        <td class="pc-birthyear">
                            <?php
                                if ($engineer->birthyear !== null) {
                                    $year = date('Y')-$engineer->birthyear;
                                    echo $year . __('years old');
                                    echo __(' (') . h($engineer->birthyear) . __('year') . ')';
                                }
                            ?>
                        </td>
                        <td class="pc-industry-exp"><?= h($this->Text->truncate($engineer->year_industory_exp, 30)) ?></td>
                        <td class="pc-skill-exp"><?= h($this->Text->truncate($engineer->skill_exp, 30)) ?></td>
                        <td class="pc-skill-exp-years"><?= h($this->Text->truncate($engineer->year_skill_exp, 30)) ?></td>
                        <td class="pc-status"><?= $this->element('parts/status_v', ['engineer' => $engineer]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->

    <div class="card-footer d-md-flex paginator">
        <div class="mr-auto" style="font-size:.8rem">
            <?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?>
        </div>
        <ul class="pagination pagination-sm">
            <?= $this->Paginator->first('<i class="fas fa-angle-double-left"></i>', ['escape' => false]) ?>
            <?= $this->Paginator->prev('<i class="fas fa-angle-left"></i>', ['escape' => false]) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next('<i class="fas fa-angle-right"></i>', ['escape' => false]) ?>
            <?= $this->Paginator->last('<i class="fas fa-angle-double-right"></i>', ['escape' => false]) ?>
        </ul>
    </div>
    <!-- /.card-footer -->
</div>
