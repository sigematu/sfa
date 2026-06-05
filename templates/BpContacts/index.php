<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BpContact[]|\Cake\Collection\CollectionInterface $bpContacts
 * @var string[]|\Cake\Collection\CollectionInterface $bps
 */
?>
<?php
$this->assign('title', __('Bp Contacts'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Bp Contacts')],
]);

$colDefs = [
    'id'       => __('Id'),
    'actions'  => __('Actions'),
    'name'     => __('Name'),
    'company'  => __('Company'),
    'email'    => __('Email'),
    'mobile'   => __('Mobile'),
    'landline' => __('Landline'),
    'position' => __('Position'),
    'status'   => __('Status'),
];
$alwaysCols = ['actions', 'name'];
?>

<p>
  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseBpContacts" aria-expanded="false" aria-controls="collapseBpContacts">
    <?= __('Open Search') ?>
  </button>
</p>
<div class="collapse" id="collapseBpContacts">
    <div class="card card-body">
        <div class="container">
            <div class="row">
                <div class="col">
                    <?php
                        echo $this->Form->create(null, ['valueSources' => 'query', 'type' => 'get']);
                        echo $this->Form->control('q', ['label' => __('Name')]);
                    ?>
                </div>
                <div class="col">
                    <?php
                        $sanitizedBps = [];
                        foreach ($bps as $id => $name) {
                            $sanitizedBps[$id] = str_replace(['株式会社', '合同会社'], '', $name);
                        }
                        echo $this->Form->control('bp_id', ['options' => $sanitizedBps, 'empty' => true, 'label' => __('Company')]);
                    ?>
                </div>
                <div class="col">
                    <?= $this->element('parts/position_e'); ?>
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
        <h2 class="card-title"></h2>
        <div class="card-toolbox">
            <?= $this->element('col_toggle', ['colToggleKey' => 'bp_contacts_hidden_cols', 'colDefs' => $colDefs, 'alwaysCols' => $alwaysCols]) ?>
            <?= $this->Paginator->limitControl([], null, [
                'label' => false,
                'class' => 'form-control-sm',
            ]); ?>
            <?= $this->Html->link(__('New'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped text-nowrap">
            <thead>
                <tr>
                    <th class="pc-id"><?= $this->Paginator->sort('id') ?></th>
                    <th class="pc-actions actions"><?= __('Actions') ?></th>
                    <th class="pc-name"><?= $this->Paginator->sort('name', __('Name')) ?></th>
                    <th class="pc-company"><?= $this->Paginator->sort('bp_id', __('Company')) ?></th>
                    <th class="pc-email"><?= $this->Paginator->sort('email') ?></th>
                    <th class="pc-mobile"><?= $this->Paginator->sort('mobile_phone', __('Mobile')) ?></th>
                    <th class="pc-landline"><?= $this->Paginator->sort('landline_phone', __('Landline')) ?></th>
                    <th class="pc-position"><?= $this->Paginator->sort('position', __('Position')) ?></th>
                    <th class="pc-status"><?= $this->Paginator->sort('status') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bpContacts as $bpContact): ?>
                <tr>
                    <td class="pc-id"><?= $this->Number->format($bpContact->id) ?></td>
                    <td class="pc-actions actions">
                        <!-- <?= $this->Html->link(__('View'), ['action' => 'view', $bpContact->id], ['class' => 'btn btn-xs btn-outline-primary', 'escape' => false]) ?> -->
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $bpContact->id], ['class' => 'btn btn-xs btn-outline-primary', 'escape' => false]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $bpContact->id], ['class' => 'btn btn-xs btn-outline-danger', 'escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $bpContact->id)]) ?>
                    </td>
                    <td class="pc-name"><?= $this->Html->link($this->Text->truncate(h($bpContact->name), 20), ['action' => 'view', $bpContact->id]) ?></td>
                    <td class="pc-company"><?= $bpContact->has('bp') ? $this->Html->link(str_replace(['株式会社', '合同会社'], '', $bpContact->bp->name), ['controller' => 'Bps', 'action' => 'view', $bpContact->bp->id]) : '' ?></td>
                    <td class="pc-email"><?= !empty($bpContact->email) ? $this->Html->link($this->Text->truncate($bpContact->email, 30), 'mailto:' . $bpContact->email) : '' ?></td>
                    <td class="pc-mobile"><?= h($bpContact->mobile_phone) ?></td>
                    <td class="pc-landline"><?= h($bpContact->landline_phone) ?></td>
                    <td class="pc-position"><?= $this->element('parts/position_v', ['bpContact' => $bpContact]) ?></td>
                    <td class="pc-status"><?= $this->element('parts/status_v', ['bpContact' => $bpContact]) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-md-flex paginator">
        <div class="mr-auto" style="font-size:.8rem">
            <?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?>
        </div>
        <ul class="pagination pagination-sm">
            <?= $this->Paginator->numbers() ?>
        </ul>
    </div>
</div>
