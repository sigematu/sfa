<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Client[]|\Cake\Collection\CollectionInterface $clients
 */
?>
<?php
$this->assign('title', __('Clients'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Clients')],
]);

$colDefs = [
    'id'           => __('Id'),
    'actions'      => __('Actions'),
    'company'      => __('Company'),
    'group-name'   => __('グループ'),
    'url'          => __('Url'),
    'sales-rank'   => __('Sales Rank'),
    'status'       => __('Status'),
];
$alwaysCols = ['actions', 'company'];
$isAdmin = $this->request->getSession()->read('Auth.role') === \App\Model\Table\UsersTable::ROLE_ADMIN;
?>

<p>
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseClient" aria-expanded="false" aria-controls="collapseClient">
        <?= __('Open Search') ?>
    </button>
</p>
<div class="collapse" id="collapseClient">
    <div class="card card-body">
        <div class="container">
            <div class="row">
                <div class="col">
                    <?php
                        echo $this->Form->create(null, ['valueSources' => 'query']);
                        echo $this->Form->control('q', ['label' => __('Company')]);
                    ?>
                </div>
                <div class="col">
                    <?= $this->element('parts/sales_rank_e'); ?>
                </div>
                <div class="col">
                    <?= $this->Form->control('group_name', ['label' => __('グループ')]); ?>
                </div>
                <div class="col">
                    <?= $this->element('parts/status_e'); ?>
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
            <?php if ($isAdmin): ?>
            <?= $this->Form->create(null, [
                'url' => ['action' => 'import'],
                'type' => 'file',
                'class' => 'd-inline-flex align-items-center mr-2',
            ]) ?>
            <?= $this->Form->file('import_file', [
                'class' => 'form-control form-control-sm mr-2',
                'accept' => '.csv',
                'label' => false,
                'required' => true,
            ]) ?>
            <?= $this->Form->button(__('Import'), ['class' => 'btn btn-secondary btn-sm']) ?>
            <?= $this->Form->end() ?>
            <?php endif; ?>
            <?= $this->element('col_toggle', ['colToggleKey' => 'clients_hidden_cols', 'colDefs' => $colDefs, 'alwaysCols' => $alwaysCols]) ?>
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
                    <th class="pc-company"><?= $this->Paginator->sort('name', __('Company')) ?></th>
                    <th class="pc-group-name"><?= $this->Paginator->sort('group_name', __('グループ')) ?></th>
                    <th class="pc-url"><?= $this->Paginator->sort('url') ?></th>
                    <th class="pc-sales-rank"><?= $this->Paginator->sort('sales_rank', __('Sales Rank')) ?></th>
                    <th class="pc-status"><?= $this->Paginator->sort('status') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client) : ?>
                    <tr>
                        <td class="pc-id"><?= $this->Number->format($client->id) ?></td>
                        <td class="pc-actions actions">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $client->id], ['class' => 'btn btn-xs btn-outline-primary', 'escape' => false]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $client->id], ['class' => 'btn btn-xs btn-outline-danger', 'escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $client->id)]) ?>
                        </td>
                        <td class="pc-company"><?= $this->Html->link($this->Text->truncate(str_replace('株式会社', '', $client->name), 30), ['action' => 'view', $client->id]) ?></td>
                        <td class="pc-group-name">
                            <?php if (!empty($client->group_name)): ?>
                                <?= $this->Html->link(
                                    $this->Text->truncate((string)$client->group_name, 30),
                                    ['action' => 'index', '?' => ['group_name' => $client->group_name]]
                                ) ?>
                            <?php endif; ?>
                        </td>
                        <td class="pc-url">
                            <?php if (!empty($client->url)): ?>
                                <?= $this->Html->link($this->Text->truncate((string)$client->url, 30), $url = $client->url, ['target' => '_blank']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="pc-sales-rank">
                            <?php if ($client->sales_rank === SALES_RANK_S): ?>
                                <?= __('S(100byen-)') ?>
                            <?php elseif ($client->sales_rank === SALES_RANK_A): ?>
                                <?= __('A(30-100byen)') ?>
                            <?php elseif ($client->sales_rank === SALES_RANK_B): ?>
                                <?= __('B(10-30byen)') ?>
                            <?php elseif ($client->sales_rank === SALES_RANK_C): ?>
                                <?= __('C(3-10byen)') ?>
                            <?php elseif ($client->sales_rank === SALES_RANK_D): ?>
                                <?= __('D(-3byen)') ?>
                            <?php endif; ?>
                        </td>
                        <td class="pc-status"><?= $this->element('parts/status_v', ['client' => $client]) ?></td>
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
