<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Bp[]|\Cake\Collection\CollectionInterface $bps
 */
?>
<?php
$this->assign('title', __('Bps'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Bps')],
]);

$colDefs = [
    'id'             => __('Id'),
    'actions'        => __('Actions'),
    'company'        => __('Company'),
    'url'            => __('Url'),
    'invoice-number' => __('Invoice Number'),
    'status'         => __('Status'),
];
$alwaysCols = ['actions', 'company'];
?>

<p>
  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseBp" aria-expanded="false" aria-controls="collapseBp">
    <?= __('Open Search') ?>
  </button>
</p>
<div class="collapse" id="collapseBp">
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
            <?= $this->element('col_toggle', ['colToggleKey' => 'bps_hidden_cols', 'colDefs' => $colDefs, 'alwaysCols' => $alwaysCols]) ?>
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
                    <th class="pc-url"><?= $this->Paginator->sort('url') ?></th>
                    <th class="pc-invoice-number"><?= $this->Paginator->sort('invoice_number', __('Invoice Number')) ?></th>
                    <th class="pc-status"><?= $this->Paginator->sort('status') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bps as $bp) : ?>
                    <tr>
                        <td class="pc-id"><?= $this->Number->format($bp->id) ?></td>
                        <td class="pc-actions actions">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $bp->id], ['class' => 'btn btn-xs btn-outline-primary', 'escape' => false]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $bp->id], ['class' => 'btn btn-xs btn-outline-danger', 'escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $bp->id)]) ?>
                        </td>
                        <td class="pc-company"><?= $this->Html->link(h($this->Text->truncate(preg_replace('/株式会社|合同会社/', '', $bp->name), 30)), ['action' => 'view', $bp->id]) ?></td>
                        <td class="pc-url"><?= $this->Html->link($this->Text->truncate($bp->url, 30), $url = $bp->url, ['target' => '_blank']) ?></td>
                        <td class="pc-invoice-number">
                            <?php
                                if(!empty($bp->invoice_number)) {
                                    echo 'T' . h($bp->invoice_number);
                                }
                            ?>
                        </td>
                        <td class="pc-status"><?= $this->element('parts/status_v', ['bp' => $bp]) ?></td>
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
