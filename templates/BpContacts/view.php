<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BpContact $bpContact
 */
?>

<?php
$this->assign('title', __('Bp Contact'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Bp Contacts'), 'url' => ['action' => 'index']],
    ['title' => __('View')],
]);
?>

<div class="view card card-primary card-outline">
  <div class="card-header">
    <h2 class="card-title"><?= h($bpContact->name) ?> <small>(<?= h($bpContact->bp->name) ?>)</small></h2>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <tr><th><?= __('Bp Contact') ?></th><td><?= h($bpContact->name) ?> (<?= h($bpContact->kana) ?>)</td></tr>
      <tr><th><?= __('Bp') ?></th><td><?= $bpContact->has('bp') ? $this->Html->link($bpContact->bp->name, ['controller' => 'Bps', 'action' => 'view', $bpContact->bp->id]) : '' ?></td></tr>
      <tr><th><?= __('Email') ?></th><td><?= h($bpContact->email) ?></td></tr>
      <tr><th><?= __('Mobile') ?></th><td><?= h($bpContact->mobile_phone) ?></td></tr>
      <tr><th><?= __('Landline') ?></th><td><?= h($bpContact->landline_phone) ?></td></tr>
      <tr>
        <th><?= __('Position') ?></th>
        <td><?= $this->element('parts/position_v', ['bpContact' => $bpContact]) ?></td>
      </tr>
      <tr><th><?= __('Status') ?></th><td><?= $bpContact->status === 1 ? __('Active') : __('Inactive') ?></td></tr>
      <tr><th><?= __('Note') ?></th><td><?= nl2br(h($bpContact->note)) ?></td></tr>
    </table>

  </div>
  <div class="card-footer d-flex">
    <div class="">
      <?= $this->Form->postLink(
          __('Delete'),
          ['action' => 'delete', $bpContact->id],
          ['confirm' => __('Are you sure you want to delete # {0}?', $bpContact->id), 'class' => 'btn btn-danger']
      ) ?>
    </div>
    <div class="ml-auto">
      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $bpContact->id], ['class' => 'btn btn-secondary']) ?>
      <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>
</div>
