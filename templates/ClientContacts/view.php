<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClientContact $clientContact
 */
?>

<?php
$this->assign('title', __('Client Contact'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Client Contacts'), 'url' => ['action' => 'index']],
    ['title' => __('View')],
]);
?>

<div class="view card card-primary card-outline">
  <div class="card-header">
    <h2 class="card-title"><?= h($clientContact->name) ?> <small>(<?= h($clientContact->client->name) ?>)</small></h2>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <tr><th><?= __('Client Contact') ?></th><td><?= h($clientContact->name) ?> (<?= h($clientContact->kana) ?>)</td></tr>
      <tr><th><?= __('Client') ?></th><td><?= $clientContact->has('client') ? $this->Html->link($clientContact->client->name, ['controller' => 'Clients', 'action' => 'view', $clientContact->client->id]) : '' ?></td></tr>
      <tr><th><?= __('Email') ?></th><td><?= h($clientContact->email) ?></td></tr>
      <tr><th><?= __('Mobile') ?></th><td><?= h($clientContact->mobile_phone) ?></td></tr>
      <tr><th><?= __('Landline') ?></th><td><?= h($clientContact->landline_phone) ?></td></tr>
      <tr><th><?= __('Department') ?></th><td><?= h($clientContact->department ?? '') ?></td></tr>
      <tr><th><?= __('役職') ?></th><td><?= h($clientContact->position_title ?? '') ?></td></tr>
      <tr>
        <th><?= __('役職クラス') ?></th>
        <td><?= $this->element('parts/position_v', ['clientContact' => $clientContact]) ?></td>
      </tr>
      <tr><th><?= __('Hierarchy') ?></th><td><?= h($hierarchyName ?? '') ?></td></tr>
      <tr><th><?= __('所在地') ?></th><td><?= h(CLIENT_CONTACT_LOCATION_LABELS[(int)($clientContact->location ?? 0)] ?? '') ?></td></tr>
      <tr><th><?= __('拠点') ?></th><td><?= h($clientContact->base ?? '') ?></td></tr>
      <tr><th><?= __('Mail Delivery Attribute') ?></th><td><?= h(CLIENT_CONTACT_CATEGORY_LABELS[(int)($clientContact->category ?? 0)] ?? '') ?></td></tr>
      <tr><th><?= __('Role') ?></th><td><?= $this->element('parts/role_v', ['clientContact' => $clientContact]) ?></td></tr>
      <tr><th><?= __('Status') ?></th><td><?= $clientContact->status === 1 ? __('Active') : __('Inactive') ?></td></tr>
      <tr><th><?= __('Note') ?></th><td><?= nl2br(h($clientContact->note)) ?></td></tr>
    </table>

  </div>
  <div class="card-footer d-flex">
    <div class="">
      <?= $this->Form->postLink(
          __('Delete'),
          ['action' => 'delete', $clientContact->id],
          ['confirm' => __('Are you sure you want to delete # {0}?', $clientContact->id), 'class' => 'btn btn-danger']
      ) ?>
    </div>
    <div class="ml-auto">
      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $clientContact->id], ['class' => 'btn btn-secondary']) ?>
      <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>
</div>
