<?php
/**
 * @var \App\View\AppView $this
 * @var array $calClientProposal
 * @var array $calClientProposalBpPic
 * @var array $calBpProcurement
 * @var array $calClientBizDev
 * @var string $currentMonth
 */

$this->assign('title', __('ダッシュボード'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('ダッシュボード')],
]);

$currentMonth = $currentMonth ?? date('Y-m');
[$cy, $cm] = explode('-', $currentMonth);
$prevMonth      = date('Y-m', mktime(0, 0, 0, (int)$cm - 1, 1, (int)$cy));
$nextMonth      = date('Y-m', mktime(0, 0, 0, (int)$cm + 1, 1, (int)$cy));
$isCurrentMonth = ($currentMonth === date('Y-m'));
$todayDay       = $isCurrentMonth ? (int)date('j') : -1;

$monthLabel = (string)($calClientProposal['monthLabel'] ?? date('Y/m'));
?>

<style>
.dash-cal-wrap { background: #fff; border-radius: 10px; box-shadow: 0 1px 6px rgba(0,0,0,.07); overflow: hidden; }
.dash-cal-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 18px 10px 14px; border-bottom: 1px solid #e8eaed; background: #fff; }
.dash-cal-section-title { font-size: 1rem; font-weight: 700; letter-spacing: -.2px; }
.dash-cal-total-badge { font-size: .82rem; font-weight: 600; color: #3c4043; background: #f1f3f4; border-radius: 20px; padding: 3px 12px; }
.dash-cal-grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
.dash-cal-grid th { padding: 7px 4px; text-align: center; font-size: .76rem; font-weight: 700; letter-spacing: .04em; color: #5f6368; background: #f8f9fa; }
.dash-cal-grid th.col-sat { color: #1a73e8; }
.dash-cal-grid th.col-sun { color: #d93025; }
.dash-cal-grid td { vertical-align: top; padding: 5px 6px 5px; border-top: 1px solid #e8eaed; height: 100px; background: #fff; }
.dash-cal-grid td.col-sat { background: #f5f8ff; }
.dash-cal-grid td.col-sun { background: #fff5f5; }
.dash-cal-grid td.day-today { background: #fef7e0; }
.dash-cal-grid td.day-empty { background: #fafafa; }
.dash-cal-grid td.col-week-sum { background: #f8f9fa; border-left: 2px solid #e8eaed; padding: 7px 9px; width: 100px; }
.day-num-wrap { display: flex; align-items: center; gap: 4px; margin-bottom: 3px; }
.day-num { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; font-size: .88rem; font-weight: 600; color: #3c4043; border-radius: 50%; flex-shrink: 0; }
.day-num.today { background: #1a73e8; color: #fff; font-weight: 700; }
.day-num.sat { color: #1a73e8; }
.day-num.sun { color: #d93025; }
.day-count-link { display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 18px; background: #e8eaed; color: #3c4043; border-radius: 9px; padding: 0 6px; font-size: .7rem; font-weight: 700; text-decoration: none; transition: background .15s; }
.day-count-link:hover { background: #dadce0; color: #202124; text-decoration: none; }
.assignee-pills { display: flex; flex-direction: column; gap: 2px; }
.assignee-pill { display: inline-flex; align-items: center; gap: 3px; border-radius: 3px; padding: 1px 6px 1px 5px; font-size: .72rem; text-decoration: none; width: fit-content; max-width: 100%; transition: opacity .15s; color: #fff; }
.assignee-pill:hover { opacity: .82; text-decoration: none; color: #fff; }
.assignee-pill .pill-cnt { font-weight: 800; font-size: .78rem; }
.week-sum-cnt { display: inline-flex; align-items: center; justify-content: center; height: 20px; background: #3c4043; color: #fff; border-radius: 10px; padding: 0 9px; font-size: .75rem; font-weight: 700; text-decoration: none; margin-bottom: 5px; }
.week-sum-cnt:hover { background: #202124; text-decoration: none; color: #fff; }
.dash-legend { padding: 10px 18px 14px; border-top: 1px solid #e8eaed; background: #fafafa; display: flex; flex-wrap: wrap; gap: 7px; align-items: center; }
.dash-legend-label { font-size: .75rem; font-weight: 600; color: #5f6368; margin-right: 2px; }
.legend-chip { display: inline-flex; align-items: center; gap: 4px; font-size: .75rem; color: #3c4043; background: #fff; border: 1px solid #e0e0e0; border-radius: 13px; padding: 2px 9px 2px 7px; }
.legend-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
.legend-total { font-size: .8rem; font-weight: 700; color: #202124; margin-left: auto; }
/* 月ナビ */
.dash-nav-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.dash-nav-left { display: flex; align-items: center; gap: 6px; }
.dash-nav-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: 1px solid #dadce0; border-radius: 50%; color: #5f6368; text-decoration: none; font-size: .85rem; transition: background .15s; }
.dash-nav-btn:hover { background: #f1f3f4; color: #202124; text-decoration: none; }
.dash-nav-month { font-size: 1.4rem; font-weight: 700; color: #202124; margin: 0 10px; letter-spacing: -.4px; }
.dash-nav-today { font-size: .8rem; padding: 4px 13px; border: 1px solid #dadce0; border-radius: 18px; color: #3c4043; text-decoration: none; transition: background .15s; }
.dash-nav-today:hover { background: #f1f3f4; text-decoration: none; }
/* 月次ステータス集計 */
.dash-summary-wrap { background: #fff; border-radius: 10px; box-shadow: 0 1px 6px rgba(0,0,0,.07); padding: 14px 18px 18px; margin-top: -8px; margin-bottom: 26px; }
.dash-summary-tabs { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 14px; }
.dash-summary-tab { border: 1px solid #dadce0; background: #fff; color: #3c4043; border-radius: 999px; padding: .26rem .8rem; font-size: .8rem; font-weight: 700; line-height: 1.2; cursor: pointer; transition: background .15s, border-color .15s; }
.dash-summary-tab:hover { background: #f1f3f4; }
.dash-summary-tab.is-active { background: var(--accent, #4361ee); border-color: var(--accent, #4361ee); color: #fff; }
.dash-summary-panel[hidden] { display: none !important; }
.dash-summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
.dash-summary-card { border: 1px solid #e8eaed; border-radius: 8px; overflow: hidden; background: #fff; }
.dash-summary-card-head { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-bottom: 2px solid; background: #f8f9fa; }
.dash-summary-card-title { font-size: .9rem; font-weight: 700; color: #202124; }
.dash-summary-card-total { font-size: .78rem; font-weight: 700; color: #3c4043; background: #fff; border-radius: 20px; padding: 2px 10px; }
.dash-summary-table { width: 100%; border-collapse: collapse; }
.dash-summary-table th { padding: 6px 10px; text-align: left; font-size: .74rem; font-weight: 700; color: #5f6368; background: #fafafa; border-bottom: 1px solid #e8eaed; }
.dash-summary-table td { padding: 6px 10px; font-size: .82rem; color: #3c4043; border-bottom: 1px solid #f1f3f4; }
.dash-summary-table th.num, .dash-summary-table td.num { text-align: right; font-variant-numeric: tabular-nums; }
.dash-summary-table tbody tr:last-child td { border-bottom: none; }
</style>

<div class="dash-nav-bar">
  <div class="dash-nav-left">
    <?= $this->Html->link('<i class="fas fa-chevron-left"></i>', ['action' => 'index', '?' => ['month' => $prevMonth]], ['class' => 'dash-nav-btn', 'escape' => false]) ?>
    <span class="dash-nav-month"><?= h($monthLabel) ?></span>
    <?= $this->Html->link('<i class="fas fa-chevron-right"></i>', ['action' => 'index', '?' => ['month' => $nextMonth]], ['class' => 'dash-nav-btn', 'escape' => false]) ?>
    <?php if (!$isCurrentMonth): ?>
      <?= $this->Html->link('今月', ['action' => 'index'], ['class' => 'dash-nav-today ml-2']) ?>
    <?php endif; ?>
  </div>
</div>

<?= $this->element('monthly_cal', [
    'calData'       => $calClientProposal,
    'title'         => '顧客提案',
    'accentColor'   => '#4361ee',
    'listUrl'       => '/sfa/client-proposals/',
    'userParamName' => 'badge_bp_pic',
    'dateFromParam' => 'date_from',
    'dateToParam'   => 'date_to',
    'todayDay'      => $todayDay,
]) ?>

<?= $this->element('monthly_status_summary', [
    'tabs'        => $summaryClientProposal,
    'accentColor' => '#4361ee',
]) ?>

<?= $this->element('monthly_cal', [
    'calData'       => $calClientProposalBpPic,
    'title'         => '顧客提案（BP担当）',
    'accentColor'   => '#0096c7',
    'listUrl'       => '/sfa/client-proposals/',
    'userParamName' => 'badge_bp_pic',
    'dateFromParam' => 'date_from',
    'dateToParam'   => 'date_to',
    'todayDay'      => $todayDay,
]) ?>

<?= $this->element('monthly_status_summary', [
    'tabs'        => $summaryClientProposalBpPic,
    'accentColor' => '#0096c7',
]) ?>

<?= $this->element('monthly_cal', [
    'calData'       => $calBpProcurement,
    'title'         => 'BP調達',
    'accentColor'   => '#2dc653',
    'listUrl'       => '/sfa/bp-procurements/',
    'userParamName' => 'badge_sender',
    'dateFromParam' => 'date_from',
    'dateToParam'   => 'date_to',
    'todayDay'      => $todayDay,
]) ?>

<?= $this->element('monthly_status_summary', [
    'tabs'        => $summaryBpProcurement,
    'accentColor' => '#2dc653',
]) ?>

<?= $this->element('monthly_cal', [
    'calData'       => $calClientBizDev,
    'title'         => '顧客案件開拓',
    'accentColor'   => '#f4a261',
    'listUrl'       => '/sfa/client-business-developments/',
    'userParamName' => 'user_id',
    'dateFromParam' => 'date_from',
    'dateToParam'   => 'date_to',
    'todayDay'      => $todayDay,
]) ?>

<?= $this->element('monthly_status_summary', [
    'tabs'        => $summaryClientBizDev,
    'accentColor' => '#f4a261',
]) ?>

<script>
  (function () {
    document.querySelectorAll('[data-summary-group]').forEach(function (group) {
      var tabs = group.querySelectorAll('[data-summary-tab]');
      var panels = group.querySelectorAll('[data-summary-panel]');
      tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
          var key = tab.getAttribute('data-summary-tab');
          tabs.forEach(function (t) {
            var active = t === tab;
            t.classList.toggle('is-active', active);
            t.setAttribute('aria-selected', active ? 'true' : 'false');
          });
          panels.forEach(function (panel) {
            panel.hidden = panel.getAttribute('data-summary-panel') !== key;
          });
        });
      });
    });
  })();
</script>
