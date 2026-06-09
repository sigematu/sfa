<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClientProposal[]|\Cake\Collection\CollectionInterface $clientProposals
 * @var array<int, \App\Model\Entity\ClientContact> $proposalContactMap
 * @var array<int, string> $salesStatusLabels
 * @var array<int, string> $salesReasonLabels
 * @var array<int, \Cake\Datasource\EntityInterface> $senderUserMap
 * @var array<int, string> $salesUserOptions
 * @var string $searchKeyword
 * @var int|null $searchStatus
 * @var int|null $searchReason
 * @var array<int, array{name:string,count:int,sender:string}> $todayBadges
 * @var array<int, array{name:string,count:int,sender:string}> $weeklyBadges
 * @var array<int, array{name:string,count:int,sender:string}> $monthlyBadges
 * @var array<int, array{name:string,count:int,bp_pic_id:string}> $todayBpBadges
 * @var array<int, array{name:string,count:int,bp_pic_id:string}> $weeklyBpBadges
 * @var array<int, array{name:string,count:int,bp_pic_id:string}> $monthlyBpBadges
 * @var array<int, array{sender_id:int,name:string,total:int,rows:array<int, array{label:string,count:int,percentage:float}>}> $monthlySalesStatusTabs
 * @var string $badgePeriod
 * @var string $badgeSender
 * @var int|null $badgeBpPic
 */
?>
<?php
$this->assign('title', __('Client Proposals'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Client Proposals')],
]);
?>

<style>
  .cp-status-interview td { background-color: #ffeaf2; }
  .cp-status-ng td { background-color: #eaf4ff; }
  .cp-status-no-reply td { background-color: #fff8d9; }
  .cp-status-unselected td {
    background-color: #ffffff;
  }

  .cp-summary-row {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 0;
    white-space: nowrap;
  }

  .cp-summary-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
    margin-bottom: 8px;
  }

  .cp-summary-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
  }

  .cp-summary-tab {
    border: 1px solid #cfd6de;
    background: #f8f9fa;
    color: #495057;
    border-radius: 999px;
    padding: 0.28rem 0.85rem;
    font-weight: 700;
    font-size: 0.85rem;
    line-height: 1.2;
    cursor: pointer;
  }

  .cp-summary-tab.is-active {
    background: #343a40;
    border-color: #343a40;
    color: #ffffff;
  }

  .cp-summary-panel[hidden] {
    display: none !important;
  }

  .cp-summary-label {
    display: inline-block;
    min-width: auto;
    font-weight: 700;
    font-size: 0.9rem;
  }

  .cp-summary-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.62rem 1.15rem;
    font-size: 1.1rem;
    line-height: 1.2;
    font-weight: 700;
    border-radius: 999px;
    text-decoration: none;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  }

  .cp-summary-row .cp-summary-badge {
    margin-right: 0 !important;
    margin-bottom: 0 !important;
  }

  .cp-summary-badge:hover {
    text-decoration: none;
    filter: brightness(0.95);
  }

  .cp-monthly-sales-status {
    margin: 10px 0 12px;
    padding: 10px;
    border: 1px solid #e6ebf0;
    border-radius: 8px;
    background: #fcfdff;
  }

  .cp-monthly-sales-title {
    margin: 0 0 8px;
    font-size: 0.95rem;
    font-weight: 700;
  }

  .cp-monthly-sales-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 10px;
  }

  .cp-monthly-sales-tab {
    border: 1px solid #cfd6de;
    background: #ffffff;
    color: #3a4651;
    border-radius: 999px;
    padding: 0.28rem 0.82rem;
    font-size: 0.86rem;
    font-weight: 700;
    line-height: 1.2;
    cursor: pointer;
  }

  .cp-monthly-sales-tab.is-active {
    background: #1f2d3d;
    border-color: #1f2d3d;
    color: #ffffff;
  }

  .cp-monthly-sales-panel[hidden] {
    display: none !important;
  }

  .cp-monthly-sales-table {
    margin-bottom: 0;
    background: #ffffff;
  }

  .cp-monthly-sales-table th,
  .cp-monthly-sales-table td {
    padding-top: 0.45rem;
    padding-bottom: 0.45rem;
    vertical-align: middle;
  }

  .cp-monthly-sales-table td:nth-child(2),
  .cp-monthly-sales-table td:nth-child(3) {
    text-align: right;
    font-variant-numeric: tabular-nums;
  }
</style>

<div class="card card-primary card-outline">
  <div class="card-header d-sm-flex">
    <h2 class="card-title"></h2>
    <div class="card-toolbox">
      <?= $this->Html->link(__('Fetch Mail'), ['action' => 'index', '?' => ['sync' => '1']], ['class' => 'btn btn-primary btn-sm']) ?>
    </div>
  </div>

  <div class="card-body border-bottom">
    <?php $activePeriod = in_array($badgePeriod, ['day', 'week', 'month'], true) ? $badgePeriod : 'day'; ?>
    <div class="cp-summary-tabs" role="tablist" aria-label="<?= __('集計期間') ?>">
      <button type="button" class="cp-summary-tab <?= $activePeriod === 'day' ? 'is-active' : '' ?>" data-period-tab="day" aria-selected="<?= $activePeriod === 'day' ? 'true' : 'false' ?>"><?= __('日次') ?></button>
      <button type="button" class="cp-summary-tab <?= $activePeriod === 'week' ? 'is-active' : '' ?>" data-period-tab="week" aria-selected="<?= $activePeriod === 'week' ? 'true' : 'false' ?>"><?= __('週次') ?></button>
      <button type="button" class="cp-summary-tab <?= $activePeriod === 'month' ? 'is-active' : '' ?>" data-period-tab="month" aria-selected="<?= $activePeriod === 'month' ? 'true' : 'false' ?>"><?= __('月次') ?></button>
    </div>

    <div class="cp-summary-panel" data-period-panel="day" <?= $activePeriod !== 'day' ? 'hidden' : '' ?>>
    <div class="cp-summary-container">
    <div class="cp-summary-row">
      <span class="cp-summary-label mr-2"><?= __('営業担当') ?></span>
      <?php $todayTotal = array_sum(array_column($todayBadges, 'count')); ?>
      <?php $todayAllActive = ($badgePeriod === 'day' && $badgeSender === ''); ?>
      <?= $this->Html->link(
          __('全員') . ': ' . h((string)$todayTotal),
          ['action' => 'index', '?' => [
              'badge_period' => 'day',
              'q' => $searchKeyword,
              'sales_status' => $searchStatus,
              'sales_reason' => $searchReason,
              'badge_bp_pic' => $badgeBpPic,
          ]],
          ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($todayAllActive ? 'badge-dark' : 'badge-secondary'), 'escape' => false]
      ) ?>
      <?php if (empty($todayBadges)): ?>
        <span class="badge badge-light cp-summary-badge">0</span>
      <?php else: ?>
        <?php foreach ($todayBadges as $badge): ?>
          <?php $isActive = ($badgePeriod === 'day' && $badgeSender === (string)$badge['sender']); ?>
          <?= $this->Html->link(
              h($badge['name']) . ': ' . h((string)$badge['count']),
              ['action' => 'index', '?' => [
                  'badge_period' => 'day',
                  'badge_sender' => (string)$badge['sender'],
                  'q' => $searchKeyword,
                  'sales_status' => $searchStatus,
                  'sales_reason' => $searchReason,
                  'badge_bp_pic' => $badgeBpPic,
              ]],
              ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($isActive ? 'badge-dark' : 'badge-danger'), 'escape' => false]
          ) ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="cp-summary-row">
      <span class="cp-summary-label mr-2"><?= __('BP担当') ?></span>
      <?php $todayBpTotal = array_sum(array_column($todayBpBadges, 'count')); ?>
      <?php $todayBpAllActive = ($badgePeriod === 'day' && $badgeBpPic === null); ?>
      <?= $this->Html->link(
          __('全員') . ': ' . h((string)$todayBpTotal),
          ['action' => 'index', '?' => [
              'badge_period' => 'day',
              'q' => $searchKeyword,
              'sales_status' => $searchStatus,
              'sales_reason' => $searchReason,
              'badge_sender' => $badgeSender,
          ]],
          ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($todayBpAllActive ? 'badge-dark' : 'badge-secondary'), 'escape' => false]
      ) ?>
      <?php if (empty($todayBpBadges)): ?>
        <span class="badge badge-light cp-summary-badge">0</span>
      <?php else: ?>
        <?php foreach ($todayBpBadges as $badge): ?>
          <?php $isActive = ($badgePeriod === 'day' && $badgeBpPic !== null && $badgeBpPic === (int)$badge['bp_pic_id']); ?>
          <?= $this->Html->link(
              h($badge['name']) . ': ' . h((string)$badge['count']),
              ['action' => 'index', '?' => [
                  'badge_period' => 'day',
                  'badge_bp_pic' => (string)$badge['bp_pic_id'],
                  'q' => $searchKeyword,
                  'sales_status' => $searchStatus,
                  'sales_reason' => $searchReason,
                  'badge_sender' => $badgeSender,
              ]],
              ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($isActive ? 'badge-dark' : 'badge-info'), 'escape' => false]
          ) ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    </div>
    </div>

    <div class="cp-summary-panel" data-period-panel="week" <?= $activePeriod !== 'week' ? 'hidden' : '' ?>>
    <div class="cp-summary-container">
    <div class="cp-summary-row">
      <span class="cp-summary-label mr-2"><?= __('営業担当') ?></span>
      <?php $weeklyTotal = array_sum(array_column($weeklyBadges, 'count')); ?>
      <?php $weeklyAllActive = ($badgePeriod === 'week' && $badgeSender === ''); ?>
      <?= $this->Html->link(
          __('全員') . ': ' . h((string)$weeklyTotal),
          ['action' => 'index', '?' => [
              'badge_period' => 'week',
              'q' => $searchKeyword,
              'sales_status' => $searchStatus,
              'sales_reason' => $searchReason,
              'badge_bp_pic' => $badgeBpPic,
          ]],
          ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($weeklyAllActive ? 'badge-dark' : 'badge-secondary'), 'escape' => false]
      ) ?>
      <?php if (empty($weeklyBadges)): ?>
        <span class="badge badge-light cp-summary-badge">0</span>
      <?php else: ?>
        <?php foreach ($weeklyBadges as $badge): ?>
          <?php $isActive = ($badgePeriod === 'week' && $badgeSender === (string)$badge['sender']); ?>
          <?= $this->Html->link(
              h($badge['name']) . ': ' . h((string)$badge['count']),
              ['action' => 'index', '?' => [
                  'badge_period' => 'week',
                  'badge_sender' => (string)$badge['sender'],
                  'q' => $searchKeyword,
                  'sales_status' => $searchStatus,
                  'sales_reason' => $searchReason,
                  'badge_bp_pic' => $badgeBpPic,
              ]],
              ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($isActive ? 'badge-dark' : 'badge-primary'), 'escape' => false]
          ) ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="cp-summary-row">
      <span class="cp-summary-label mr-2"><?= __('BP担当') ?></span>
      <?php $weeklyBpTotal = array_sum(array_column($weeklyBpBadges, 'count')); ?>
      <?php $weeklyBpAllActive = ($badgePeriod === 'week' && $badgeBpPic === null); ?>
      <?= $this->Html->link(
          __('全員') . ': ' . h((string)$weeklyBpTotal),
          ['action' => 'index', '?' => [
              'badge_period' => 'week',
              'q' => $searchKeyword,
              'sales_status' => $searchStatus,
              'sales_reason' => $searchReason,
              'badge_sender' => $badgeSender,
          ]],
          ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($weeklyBpAllActive ? 'badge-dark' : 'badge-secondary'), 'escape' => false]
      ) ?>
      <?php if (empty($weeklyBpBadges)): ?>
        <span class="badge badge-light cp-summary-badge">0</span>
      <?php else: ?>
        <?php foreach ($weeklyBpBadges as $badge): ?>
          <?php $isActive = ($badgePeriod === 'week' && $badgeBpPic !== null && $badgeBpPic === (int)$badge['bp_pic_id']); ?>
          <?= $this->Html->link(
              h($badge['name']) . ': ' . h((string)$badge['count']),
              ['action' => 'index', '?' => [
                  'badge_period' => 'week',
                  'badge_bp_pic' => (string)$badge['bp_pic_id'],
                  'q' => $searchKeyword,
                  'sales_status' => $searchStatus,
                  'sales_reason' => $searchReason,
                  'badge_sender' => $badgeSender,
              ]],
              ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($isActive ? 'badge-dark' : 'badge-info'), 'escape' => false]
          ) ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    </div>
    </div>

    <div class="cp-summary-panel" data-period-panel="month" <?= $activePeriod !== 'month' ? 'hidden' : '' ?>>
    <div class="cp-summary-container">
    <div class="cp-summary-row">
      <span class="cp-summary-label mr-2"><?= __('営業担当') ?></span>
      <?php $monthlyTotal = array_sum(array_column($monthlyBadges, 'count')); ?>
      <?php $monthlyAllActive = ($badgePeriod === 'month' && $badgeSender === ''); ?>
      <?= $this->Html->link(
          __('全員') . ': ' . h((string)$monthlyTotal),
          ['action' => 'index', '?' => [
              'badge_period' => 'month',
              'q' => $searchKeyword,
              'sales_status' => $searchStatus,
              'sales_reason' => $searchReason,
              'badge_bp_pic' => $badgeBpPic,
          ]],
          ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($monthlyAllActive ? 'badge-dark' : 'badge-secondary'), 'escape' => false]
      ) ?>
      <?php if (empty($monthlyBadges)): ?>
        <span class="badge badge-light cp-summary-badge">0</span>
      <?php else: ?>
        <?php foreach ($monthlyBadges as $badge): ?>
          <?php $isActive = ($badgePeriod === 'month' && $badgeSender === (string)$badge['sender']); ?>
          <?= $this->Html->link(
              h($badge['name']) . ': ' . h((string)$badge['count']),
              ['action' => 'index', '?' => [
                  'badge_period' => 'month',
                  'badge_sender' => (string)$badge['sender'],
                  'q' => $searchKeyword,
                  'sales_status' => $searchStatus,
                  'sales_reason' => $searchReason,
                  'badge_bp_pic' => $badgeBpPic,
              ]],
              ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($isActive ? 'badge-dark' : 'badge-success'), 'escape' => false]
          ) ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="cp-summary-row">
      <span class="cp-summary-label mr-2"><?= __('BP担当') ?></span>
      <?php $monthlyBpTotal = array_sum(array_column($monthlyBpBadges, 'count')); ?>
      <?php $monthlyBpAllActive = ($badgePeriod === 'month' && $badgeBpPic === null); ?>
      <?= $this->Html->link(
          __('全員') . ': ' . h((string)$monthlyBpTotal),
          ['action' => 'index', '?' => [
              'badge_period' => 'month',
              'q' => $searchKeyword,
              'sales_status' => $searchStatus,
              'sales_reason' => $searchReason,
              'badge_sender' => $badgeSender,
          ]],
          ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($monthlyBpAllActive ? 'badge-dark' : 'badge-secondary'), 'escape' => false]
      ) ?>
      <?php if (empty($monthlyBpBadges)): ?>
        <span class="badge badge-light cp-summary-badge">0</span>
      <?php else: ?>
        <?php foreach ($monthlyBpBadges as $badge): ?>
          <?php $isActive = ($badgePeriod === 'month' && $badgeBpPic !== null && $badgeBpPic === (int)$badge['bp_pic_id']); ?>
          <?= $this->Html->link(
              h($badge['name']) . ': ' . h((string)$badge['count']),
              ['action' => 'index', '?' => [
                  'badge_period' => 'month',
                  'badge_bp_pic' => (string)$badge['bp_pic_id'],
                  'q' => $searchKeyword,
                  'sales_status' => $searchStatus,
                  'sales_reason' => $searchReason,
                  'badge_sender' => $badgeSender,
              ]],
              ['class' => 'badge cp-summary-badge mr-2 mb-2 ' . ($isActive ? 'badge-dark' : 'badge-info'), 'escape' => false]
          ) ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    </div>
    </div>

    <?= $this->Form->create(null, ['type' => 'get', 'class' => 'form-inline']) ?>
    <div class="form-group mr-2 mb-2">
      <?= $this->Form->control('q', [
          'label' => false,
          'value' => $searchKeyword,
          'placeholder' => __('宛先・件名で検索'),
          'class' => 'form-control form-control-sm',
      ]) ?>
    </div>
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
      <?= $this->Form->button(__('Search'), ['class' => 'btn btn-primary btn-sm']) ?>
      <?= $this->Html->link(__('Reset'), ['action' => 'index'], ['class' => 'btn btn-default btn-sm ml-2']) ?>
    </div>
    <?= $this->Form->end() ?>

    <div class="cp-monthly-sales-status">
      <h3 class="cp-monthly-sales-title"><?= __('営業担当別 月次営業状況') ?></h3>
      <?php if (empty($monthlySalesStatusTabs)): ?>
        <div class="text-muted"><?= __('今月の集計データはありません。') ?></div>
      <?php else: ?>
        <?php $activeSalesTab = (string)$monthlySalesStatusTabs[0]['sender_id']; ?>
        <div class="cp-monthly-sales-tabs" role="tablist" aria-label="<?= __('営業担当別月次集計') ?>">
          <?php foreach ($monthlySalesStatusTabs as $tab): ?>
            <?php $tabId = (string)$tab['sender_id']; ?>
            <button
              type="button"
              class="cp-monthly-sales-tab <?= $tabId === $activeSalesTab ? 'is-active' : '' ?>"
              data-sales-month-tab="<?= h($tabId) ?>"
              aria-selected="<?= $tabId === $activeSalesTab ? 'true' : 'false' ?>"
            >
              <?= h($tab['name']) ?> (<?= h((string)$tab['total']) ?>)
            </button>
          <?php endforeach; ?>
        </div>

        <?php foreach ($monthlySalesStatusTabs as $tab): ?>
          <?php $tabId = (string)$tab['sender_id']; ?>
          <div class="cp-monthly-sales-panel" data-sales-month-panel="<?= h($tabId) ?>" <?= $tabId !== $activeSalesTab ? 'hidden' : '' ?>>
            <table class="table table-sm table-bordered cp-monthly-sales-table">
              <thead>
                <tr>
                  <th><?= __('営業状況') ?></th>
                  <th><?= __('件数') ?></th>
                  <th><?= __('割合') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($tab['rows'] as $row): ?>
                  <tr>
                    <td><?= h($row['label']) ?></td>
                    <td><?= h((string)$row['count']) ?></td>
                    <td><?= h(number_format((float)$row['percentage'], 1)) ?>%</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <thead>
        <tr>
          <th><?= __('Actions') ?></th>
          <th><?= __('営業状況') ?></th>
          <th><?= __('事由') ?></th>
          <th><?= __('Date Time') ?></th>
          <th><?= __('営業担当') ?></th>
          <th><?= __('BP担当') ?></th>
          <th><?= __('顧客') ?></th>
          <th><?= __('Subject') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($clientProposals->toArray())): ?>
          <tr>
            <td colspan="8" class="text-muted"><?= __('No proposals found.') ?></td>
          </tr>
        <?php else: ?>
          <?php foreach ($clientProposals as $proposal): ?>
            <?php
              $statusClass = 'cp-status-unselected';
              if ((int)$proposal->sales_status === CLIENT_PROPOSAL_SALES_STATUS_INTERVIEW) {
                  $statusClass = 'cp-status-interview';
              } elseif (
                  (int)$proposal->sales_status === CLIENT_PROPOSAL_SALES_STATUS_CLIENT_NG ||
                  (int)$proposal->sales_status === CLIENT_PROPOSAL_SALES_STATUS_DECLINED
              ) {
                  $statusClass = 'cp-status-ng';
              } elseif ((int)$proposal->sales_status === CLIENT_PROPOSAL_SALES_STATUS_NO_REPLY) {
                  $statusClass = 'cp-status-no-reply';
              }
            ?>
            <tr class="<?= h($statusClass) ?>">
              <?php $proposalFormId = 'proposal-sales-form-' . (int)$proposal->id; ?>
              <?php $salesStatusValue = $proposal->sales_status !== null ? (int)$proposal->sales_status : CLIENT_PROPOSAL_SALES_STATUS_PROPOSING; ?>
              <?php $salesReasonValue = $proposal->sales_reason !== null ? (int)$proposal->sales_reason : CLIENT_PROPOSAL_REASON_UNSET; ?>
              <td>
                <?= $this->Form->create(null, [
                    'url' => ['action' => 'updateSalesResult'],
                    'id' => $proposalFormId,
                    'class' => 'd-flex align-items-center',
                ]) ?>
                <?= $this->Form->hidden('proposal_id', ['value' => (int)$proposal->id]) ?>
                <?= $this->Form->button(__('保存'), ['class' => 'btn btn-secondary btn-sm']) ?>
                <?= $this->Form->end() ?>
              </td>
              <td>
                <select
                  name="sales_status"
                  form="<?= h($proposalFormId) ?>"
                  class="form-control form-control-sm"
                >
                  <?php foreach ($salesStatusLabels as $value => $label): ?>
                    <option value="<?= h((string)$value) ?>" <?= ($salesStatusValue === (int)$value) ? 'selected' : '' ?>>
                      <?= h($label) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td>
                <select
                  name="sales_reason"
                  form="<?= h($proposalFormId) ?>"
                  class="form-control form-control-sm"
                >
                  <?php foreach ($salesReasonLabels as $value => $label): ?>
                    <option value="<?= h((string)$value) ?>" <?= ($salesReasonValue === (int)$value) ? 'selected' : '' ?>>
                      <?= h($label) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td><?= h($proposal->received_at) ?></td>
              <td>
                <?php
                  $senderRaw = (string)$proposal->sender;
                  $senderLabel = $senderRaw;
                  if (ctype_digit($senderRaw) && isset($senderUserMap[(int)$senderRaw])) {
                      $senderUser = $senderUserMap[(int)$senderRaw];
                      $senderName = (string)($senderUser->display_name ?? '');
                      if ($senderName === '') {
                        $senderName = (string)($senderUser->username ?? '');
                      }
                      $senderLabel = $senderName !== '' ? $senderName : $senderRaw;
                  }
                ?>
                <?= h($this->Text->truncate($senderLabel, 40)) ?>
              </td>
              <td>
                <select
                  name="bp_pic_id"
                  form="<?= h($proposalFormId) ?>"
                  class="form-control form-control-sm"
                >
                  <option value=""><?= __('未設定') ?></option>
                  <?php foreach ($salesUserOptions as $value => $label): ?>
                    <option value="<?= h((string)$value) ?>" <?= ((int)($proposal->bp_pic_id ?? 0) === (int)$value) ? 'selected' : '' ?>>
                      <?= h($label) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td>
                <?php if (!empty($proposalContactMap[$proposal->id])): ?>
                  <?php $contact = $proposalContactMap[$proposal->id]; ?>
                  <?php if ($contact->has('client')): ?>
                    <?php
                      $companyNameRaw = str_replace(['株式会社', '合同会社'], '', (string)$contact->client->name);
                      $companyNameSanitized = trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $companyNameRaw) ?? '');
                    ?>
                    <?= $this->Html->link(
                      h($this->Text->truncate($companyNameSanitized, 15)),
                        ['controller' => 'Clients', 'action' => 'view', $contact->client->id]
                    , ['escape' => false]) ?>
                  <?php else: ?>
                    <?= __('Client') ?>
                  <?php endif; ?>
                  /
                  <?= $this->Html->link(
                      (string)$contact->name,
                      ['controller' => 'ClientContacts', 'action' => 'view', $contact->id]
                  ) ?>
                <?php else: ?>
                  <?php
                    $rawRecipient = (string)$proposal->recipient;
                    preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $rawRecipient, $recipientMatch);
                    $recipientEmail = strtolower((string)($recipientMatch[0] ?? ''));
                  ?>
                  <?= h($this->Text->truncate($rawRecipient, 40)) ?>
                  <div class="mt-1">
                    <?= $this->Html->link(
                        __('Add Client'),
                        ['controller' => 'Clients', 'action' => 'add'],
                        ['class' => 'btn btn-xs btn-outline-secondary mr-1']
                    ) ?>
                    <?= $this->Html->link(
                        __('Add Client Contact'),
                        ['controller' => 'ClientContacts', 'action' => 'add', '?' => ['email' => $recipientEmail]],
                        ['class' => 'btn btn-xs btn-outline-secondary']
                    ) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <?php
                  $sanitizedSubject = trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string)$proposal->subject) ?? '');
                ?>
                <?= $this->Html->link(
                  h($this->Text->truncate($sanitizedSubject, 40)),
                    ['action' => 'view', $proposal->id],
                    ['escape' => false]
                ) ?>
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

<script>
  (function () {
    var tabs = document.querySelectorAll('[data-period-tab]');
    var panels = document.querySelectorAll('[data-period-panel]');
    if (!tabs.length || !panels.length) {
      return;
    }

    function setActive(period) {
      tabs.forEach(function (tab) {
        var active = tab.getAttribute('data-period-tab') === period;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-selected', active ? 'true' : 'false');
      });
      panels.forEach(function (panel) {
        panel.hidden = panel.getAttribute('data-period-panel') !== period;
      });
    }

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        setActive(tab.getAttribute('data-period-tab'));
      });
    });
  })();

  (function () {
    var tabs = document.querySelectorAll('[data-sales-month-tab]');
    var panels = document.querySelectorAll('[data-sales-month-panel]');
    if (!tabs.length || !panels.length) {
      return;
    }

    function setActive(tabId) {
      tabs.forEach(function (tab) {
        var active = tab.getAttribute('data-sales-month-tab') === tabId;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-selected', active ? 'true' : 'false');
      });
      panels.forEach(function (panel) {
        panel.hidden = panel.getAttribute('data-sales-month-panel') !== tabId;
      });
    }

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        setActive(tab.getAttribute('data-sales-month-tab'));
      });
    });
  })();
</script>
