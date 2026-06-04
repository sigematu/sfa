<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClientProposal[]|\Cake\Collection\CollectionInterface $clientProposals
 * @var array<int, \App\Model\Entity\ClientContact> $proposalContactMap
 */
?>
<?php
$this->assign('title', __('Client Proposals'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Client Proposals')],
]);
?>

<div class="card card-primary card-outline">
  <div class="card-header d-sm-flex">
    <h2 class="card-title"></h2>
    <div class="card-toolbox">
      <?= $this->Html->link(__('Fetch Mail'), ['action' => 'index', '?' => ['sync' => '1']], ['class' => 'btn btn-primary btn-sm']) ?>
    </div>
  </div>

  <div class="card-body table-responsive p-0">
    <table class="table table-hover table-striped text-nowrap">
      <thead>
        <tr>
          <th><?= __('Date Time') ?></th>
          <th><?= __('Sender') ?></th>
          <th><?= __('Recipient') ?></th>
          <th><?= __('Subject') ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($clientProposals->toArray())): ?>
          <tr>
            <td colspan="5" class="text-muted"><?= __('No proposals found.') ?></td>
          </tr>
        <?php else: ?>
          <?php foreach ($clientProposals as $proposal): ?>
            <tr>
              <td><?= h($proposal->received_at) ?></td>
              <td><?= h($this->Text->truncate((string)$proposal->sender, 40)) ?></td>
              <td>
                <?php if (!empty($proposalContactMap[$proposal->id])): ?>
                  <?php $contact = $proposalContactMap[$proposal->id]; ?>
                  <?php if ($contact->has('client')): ?>
                    <?= $this->Html->link(
                        str_replace(['株式会社', '合同会社'], '', (string)$contact->client->name),
                        ['controller' => 'Clients', 'action' => 'view', $contact->client->id]
                    ) ?>
                  <?php else: ?>
                    <?= __('Client') ?>
                  <?php endif; ?>
                  /
                  <?= $this->Html->link(
                      (string)$contact->name,
                      ['controller' => 'ClientContacts', 'action' => 'view', $contact->id]
                  ) ?>
                <?php else: ?>
                  <?= h($this->Text->truncate((string)$proposal->recipient, 40)) ?>
                <?php endif; ?>
              </td>
              <td><?= h($this->Text->truncate((string)$proposal->subject, 80)) ?></td>
              <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $proposal->id], ['class' => 'btn btn-xs btn-outline-primary']) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

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
</div>
