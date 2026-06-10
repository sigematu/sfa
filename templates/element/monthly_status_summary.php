<?php
/**
 * 月単位のステータス別件数/割合サマリ（全体＋担当者別タブ）
 *
 * @var \App\View\AppView $this
 * @var array<int, array{key:string, name:string, total:int, sections:array<int, array{title:string, total:int, rows:array<int, array{label:string, count:int, percentage:float}>}>}> $tabs
 * @var string $accentColor
 */
$tabs        = $tabs ?? [];
$accentColor = $accentColor ?? '#4361ee';
?>
<div class="dash-summary-wrap" data-summary-group style="--accent: <?= h($accentColor) ?>;">
  <div class="dash-summary-tabs" role="tablist">
    <?php foreach ($tabs as $i => $tab): ?>
      <button
        type="button"
        class="dash-summary-tab <?= $i === 0 ? 'is-active' : '' ?>"
        data-summary-tab="<?= h($tab['key']) ?>"
        aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
      ><?= h($tab['name']) ?> (<?= h((string)$tab['total']) ?>)</button>
    <?php endforeach; ?>
  </div>

  <?php foreach ($tabs as $i => $tab): ?>
    <div class="dash-summary-panel" data-summary-panel="<?= h($tab['key']) ?>" <?= $i === 0 ? '' : 'hidden' ?>>
      <div class="dash-summary-grid">
        <?php foreach ($tab['sections'] as $section): ?>
          <div class="dash-summary-card">
            <div class="dash-summary-card-head">
              <span class="dash-summary-card-title"><?= h($section['title']) ?></span>
              <span class="dash-summary-card-total"><?= h((string)$section['total']) ?>件</span>
            </div>
            <table class="dash-summary-table">
              <thead>
                <tr>
                  <th><?= __('ステータス') ?></th>
                  <th class="num"><?= __('件数') ?></th>
                  <th class="num"><?= __('割合') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($section['rows'] as $row): ?>
                  <tr>
                    <td><?= h($row['label']) ?></td>
                    <td class="num"><?= h((string)$row['count']) ?></td>
                    <td class="num"><?= h(number_format((float)$row['percentage'], 1)) ?>%</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
