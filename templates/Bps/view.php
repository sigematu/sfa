<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Bp $bp
 */
?>

<?php
$this->assign('title', __('Bp'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Bps'), 'url' => ['action' => 'index']],
    ['title' => __('View')],
]);
?>

<div class="view card card-primary card-outline">
  <div class="card-header d-sm-flex">
    <h2 class="card-title"><?= h($bp->name) ?></h2>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <tr>
          <th><?= __('Id') ?></th>
          <td><?= $this->Number->format($bp->id) ?></td>
      </tr>
      <tr>
          <th><?= __('Company') ?></th>
          <td><?= h($bp->name) ?></td>
      </tr>
      <tr>
          <th><?= __('Company Kana') ?></th>
          <td><?= h($bp->kana) ?></td>
      </tr>
      <tr>
          <th><?= __('Url') ?></th>
          <td><?= $this->Html->link($bp->url, $url = $bp->url, ['target' => '_blank']) ?></td>
      </tr>
      <tr>
          <th><?= __('Invoice Number') ?></th>
          <td>
            <?php
              if(!empty($bp->invoice_number)) {
                echo 'T' . h($bp->invoice_number);
              }
            ?>
          </td>
      </tr>

      <tr>
          <th><?= __('Note') ?></th>
          <td><?= nl2br(h($bp->note) ?? '') ?></td>
      </tr>
      <tr>
          <th><?= __('Status') ?></th>
          <td>
            <?php if ($bp->status === STATUS_ACTIVE): ?>
              <?= __('Active') ?>
            <?php else: ?>
              <div class="text-muted"><?= __('Inactive') ?></div>
            <?php endif; ?>
          </td>
      </tr>
      <tr>
          <th><?= __('Created User') ?></th>
          <td><?= h($bp->created_dn) ?></td>
          </tr>
      <tr>
          <th><?= __('Created') ?></th>
          <td><?= h($bp->created) ?></td>
      </tr>
      <tr>
          <th><?= __('Modified User') ?></th>
          <td><?= h($bp->modified_dn) ?></td>
          </tr>
      <tr>
      <tr>
          <th><?= __('Modified') ?></th>
          <td><?= h($bp->modified) ?></td>
      </tr>
    </table>
  </div>
  <div class="card-footer d-flex">
    <div class="">
      <?= $this->Form->postLink(
          __('Delete'),
          ['action' => 'delete', $bp->id],
          ['confirm' => __('Are you sure you want to delete # {0}?', $bp->id), 'class' => 'btn btn-danger']
      ) ?>
    </div>
    <div class="ml-auto">
      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $bp->id], ['class' => 'btn btn-secondary']) ?>
      <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>
</div>

<div class="related related-contacts view card">
  <div class="card-header d-sm-flex">
    <h3 class="card-title"><?= __('Related Contacts') ?> <small class="text-muted ml-2" style="font-size:75%;"><?= __('Showing the most recent {0} records', 20) ?></small></h3>
    <div class="card-toolbox">
      <?= $this->Html->link(__('List '), ['controller' => 'BpContacts', 'action' => 'index', '?' => ['bp_id' => $bp->id]], ['class' => 'btn btn-primary btn-sm']) ?>
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
        <?php if (empty($bp->bp_contacts)): ?>
          <tr><td colspan="6" class="text-muted"><?= __('No contacts found.') ?></td></tr>
        <?php else: ?>
          <?php foreach ($bp->bp_contacts as $contact): ?>
            <tr>
              <td><?= $this->Html->link($this->Text->truncate(h($contact->name), 20), ['controller' => 'BpContacts', 'action' => 'view', $contact->id]) ?> (<?= $this->Text->truncate(h($contact->kana), 20) ?>)</td>
              <td><?= $this->Text->truncate(h($contact->email), 30) ?></td>
              <td><?= h($contact->mobile_phone) ?></td>
              <td><?= h($contact->landline_phone) ?></td>
              <td><?= $this->element('parts/position_v', ['bpContact' => $contact]) ?></td>
              <td><?= $this->element('parts/status_v', ['bpContact' => $contact]) ?></td>
              <td class="actions">
                <?= $this->Html->link(__('Edit'), ['controller' => 'BpContacts', 'action' => 'edit', $contact->id], ['class'=>'btn btn-xs btn-outline-primary']) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
