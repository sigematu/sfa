<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Client $client
 */
?>

<?php
$this->assign('title', __('Client'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Clients'), 'url' => ['action' => 'index']],
    ['title' => __('View')],
]);
?>

<div class="view card card-primary card-outline">
  <div class="card-header d-sm-flex">
    <h2 class="card-title"><?= h($client->name) ?></h2>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <tr>
          <th><?= __('Id') ?></th>
          <td><?= $this->Number->format($client->id) ?></td>
      </tr>
      <tr>
          <th><?= __('Company') ?></th>
          <td><?= h($client->name) ?></td>
      </tr>
      <tr>
          <th><?= __('Company Kana') ?></th>
          <td><?= h($client->kana) ?></td>
      </tr>
      <tr>
          <th><?= __('Url') ?></th>
          <td>
            <?php if (!empty($client->url)): ?>
              <?= $this->Html->link((string)$client->url, $url = $client->url, ['target' => '_blank']) ?>
            <?php endif; ?>
          </td>
      </tr>
      <tr>
          <th><?= __('Sales Rank') ?></th>
          <td>
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
      </tr>
      <tr>
          <th><?= __('口座') ?></th>
          <td><?= (int)($client->account ?? 0) === 1 ? __('あり') : __('なし') ?></td>
      </tr>
            <tr>
              <th><?= __('グループ') ?></th>
              <td><?= h($client->group_name ?? '') ?></td>
            </tr>
      <tr>
          <th><?= __('Note') ?></th>
          <td><?= nl2br(h($client->note) ?? '') ?></td>
      </tr>
      <tr>
          <th><?= __('Status') ?></th>
          <td>
              <?php if ($client->status === STATUS_ACTIVE): ?>
                <?= __('Active') ?>
              <?php else: ?>
                <div class="text-muted"><?= __('Inactive') ?></div>
              <?php endif; ?>
          </td>
      </tr>
      <tr>
          <th><?= __('Created User') ?></th>
          <td><?= h($client->created_dn) ?></td>
          </tr>
      <tr>
          <th><?= __('Created') ?></th>
          <td><?= h($client->created) ?></td>
      </tr>
      <tr>
          <th><?= __('Modified User') ?></th>
          <td><?= h($client->modified_dn) ?></td>
          </tr>
      <tr>
      <tr>
          <th><?= __('Modified') ?></th>
          <td><?= h($client->modified) ?></td>
      </tr>
    </table>
  </div>
  <div class="card-footer d-flex">
    <div class="">
      <?= $this->Form->postLink(
          __('Delete'),
          ['action' => 'delete', $client->id],
          ['confirm' => __('Are you sure you want to delete # {0}?', $client->id), 'class' => 'btn btn-danger']
      ) ?>
    </div>
    <div class="ml-auto">
      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $client->id], ['class' => 'btn btn-secondary']) ?>
      <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>
</div>

<div class="related related-contacts view card">
  <div class="card-header d-sm-flex">
    <h3 class="card-title"><?= __('Related Contacts') ?> <small class="text-muted ml-2" style="font-size:75%;"><?= __('Showing the most recent {0} records', 20) ?></small></h3>
    <div class="card-toolbox">
      <?= $this->Html->link(__('List '), ['controller' => 'ClientContacts', 'action' => 'index', '?' => ['client_id' => $client->id]], ['class' => 'btn btn-primary btn-sm']) ?>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <thead>
        <tr>
          <th><?= __('Name') ?></th>
          <th><?= __('Email') ?></th>
          <th><?= __('Mobile') ?></th>
          <th><?= __('Landline') ?></th>
          <th><?= __('Position') ?></th>
          <th><?= __('Status') ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($client->client_contacts)): ?>
          <tr><td colspan="6" class="text-muted"><?= __('No contacts found.') ?></td></tr>
        <?php else: ?>
          <?php foreach ($client->client_contacts as $contact): ?>
            <tr>
              <td><?= $this->Html->link($this->Text->truncate(h($contact->name), 20), ['controller' => 'ClientContacts', 'action' => 'view', $contact->id]) ?> (<?= $this->Text->truncate((string)($contact->kana ?? ''), 20) ?>)</td>
              <td><?= $this->Text->truncate(h($contact->email), 30) ?></td>
              <td><?= h($contact->mobile_phone) ?></td>
              <td><?= h($contact->landline_phone) ?></td>
              <td><?= $this->element('parts/position_v', ['clientContact' => $contact]) ?></td>
              <td><?= $this->element('parts/status_v', ['clientContact' => $contact]) ?></td>
              <td class="actions">
                <?= $this->Html->link(__('Edit'), ['controller' => 'ClientContacts', 'action' => 'edit', $contact->id], ['class'=>'btn btn-xs btn-outline-primary']) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
