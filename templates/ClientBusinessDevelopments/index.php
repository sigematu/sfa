<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClientBusinessDevelopment[]|\Cake\Collection\CollectionInterface $records
 * @var array<int, string> $salesStatusLabels
 * @var array<int, string> $salesReasonLabels
 * @var array<int, string> $userOptions
 * @var int|null $searchStatus
 * @var int|null $searchReason
 * @var int $searchUserId
 */
?>
<?php
$this->assign('title', __('顧客案件開拓'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('顧客案件開拓')],
]);
?>

<div class="card card-primary card-outline">
  <div class="card-header d-sm-flex">
    <h2 class="card-title"></h2>
    <div class="card-toolbox">
      <?= $this->Html->link(__('新規追加'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
    </div>
  </div>

  <div class="card-body border-bottom">
    <?= $this->Form->create(null, ['type' => 'get', 'class' => 'form-inline']) ?>
    <div class="form-group mr-2 mb-2">
      <?= $this->Form->control('sales_status', [
          'label' => false,
          'empty' => __('営業状況(すべて)'),
          'options' => $salesStatusLabels,
          'value' => $searchStatus,
          'class' => 'form-control form-control-sm',
      ]) ?>
    </div>
    <div class="form-group mr-2 mb-2">
      <?= $this->Form->control('sales_reason', [
          'label' => false,
          'empty' => __('事由(すべて)'),
          'options' => $salesReasonLabels,
          'value' => $searchReason,
          'class' => 'form-control form-control-sm',
      ]) ?>
    </div>
    <div class="form-group mr-2 mb-2">
      <?= $this->Form->control('user_id', [
          'label' => false,
          'empty' => __('営業担当(すべて)'),
          'options' => $userOptions,
          'value' => $searchUserId > 0 ? $searchUserId : '',
          'class' => 'form-control form-control-sm',
      ]) ?>
    </div>
    <div class="form-group mr-2 mb-2">
      <?= $this->Form->button(__('Search'), ['class' => 'btn btn-primary btn-sm']) ?>
      <?= $this->Html->link(__('Reset'), ['action' => 'index'], ['class' => 'btn btn-default btn-sm ml-2']) ?>
    </div>
    <?= $this->Form->end() ?>
  </div>

  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <thead>
        <tr>
          <th><?= __('アクション') ?></th>
          <th><?= __('営業状況') ?></th>
          <th><?= __('事由') ?></th>
          <th><?= __('日時') ?></th>
          <th><?= __('営業担当') ?></th>
          <th><?= __('顧客') ?></th>
          <th><?= __('状況') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($records->toArray())): ?>
          <tr>
            <td colspan="7" class="text-muted"><?= __('データがありません。') ?></td>
          </tr>
        <?php else: ?>
          <?php foreach ($records as $record): ?>
            <tr>
              <td>
                <?= $this->Html->link(__('編集'), ['action' => 'edit', $record->id], ['class' => 'btn btn-xs btn-outline-secondary']) ?>
              </td>
              <td><?= h($salesStatusLabels[(int)($record->sales_status ?? 0)] ?? '') ?></td>
              <td><?= h($salesReasonLabels[(int)($record->sales_reason ?? CLIENT_BIZ_DEV_REASON_UNSET)] ?? '') ?></td>
              <td><?= $record->action_at ? h($record->action_at->format('Y/m/d H:i')) : '' ?></td>
              <td>
                <?php if (!empty($record->user)): ?>
                  <?php
                    $userName = (string)($record->user->display_name ?? '');
                    if ($userName === '') {
                        $userName = (string)($record->user->username ?? '');
                    }
                  ?>
                  <?= h($userName) ?>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($record->client)): ?>
                  <?= $this->Html->link(
                      h($this->Text->truncate((string)$record->client->name, 20)),
                      ['controller' => 'Clients', 'action' => 'view', $record->client->id],
                      ['escape' => false]
                  ) ?>
                  <?php if (!empty($record->client_contact)): ?>
                    /
                    <?= $this->Html->link(
                        (string)$record->client_contact->name,
                        ['controller' => 'ClientContacts', 'action' => 'view', $record->client_contact->id]
                    ) ?>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td style="white-space:pre-wrap;max-width:200px"><?= h((string)($record->status ?? '')) ?></td>
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
