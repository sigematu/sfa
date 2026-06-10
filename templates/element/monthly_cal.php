<?php
/**
 * 月次カレンダー汎用エレメント
 *
 * @var \App\View\AppView $this
 * @var array{monthLabel:string,daysInMonth:int,firstWeekday:int,grandTotal:int,byDay:array,assigneeTotals:array} $calData
 * @var string  $title          カレンダータイトル
 * @var string  $accentColor    ヘッダーアクセントカラー (CSS color)
 * @var string  $listUrl        一覧ページのベースURL
 * @var string  $userParamName  担当者フィルター用URLパラメータ名
 * @var string  $dateFromParam  日付範囲fromのパラメータ名 (空文字ならリンクに日付を付与しない)
 * @var string  $dateToParam    日付範囲toのパラメータ名
 * @var int     $todayDay       今日の日 (対象月以外なら -1)
 */

$weekLabels  = ['月', '火', '水', '木', '金', '土', '日'];
$badgePalette = ['#4361ee','#e63946','#2dc653','#f4a261','#7b2d8b','#0096c7','#c9184a','#6d6875','#d97706','#0891b2'];

// userId をキーにして色を固定（カレンダー・月をまたいで同一ユーザーが同じ色になる）
$badgeColor = [];
foreach ((array)$calData['assigneeTotals'] as $assignee) {
    $uid = (int)($assignee['userId'] ?? 0);
    $n   = (string)$assignee['name'];
    $badgeColor[$n] = $uid > 0
        ? $badgePalette[$uid % count($badgePalette)]
        : '#6c757d';
}

$daysInMonth = (int)$calData['daysInMonth'];
$monthLabel  = (string)$calData['monthLabel'];
$yearStr     = substr($monthLabel, 0, 4);
$monStr      = substr($monthLabel, 5, 2);

$calendarCells = [];
for ($i = 0; $i < (int)$calData['firstWeekday']; $i++) {
    $calendarCells[] = null;
}
for ($d = 1; $d <= $daysInMonth; $d++) {
    $calendarCells[] = $d;
}
while (count($calendarCells) % 7 !== 0) {
    $calendarCells[] = null;
}
$calendarWeeks = array_chunk($calendarCells, 7);

$grandTotal = (int)$calData['grandTotal'];
?>

<div class="dash-cal-wrap mb-4">

  <div class="dash-cal-header" style="border-left: 4px solid <?= h($accentColor) ?>;">
    <div>
      <span class="dash-cal-section-title" style="color:<?= h($accentColor) ?>;"><?= h($title) ?></span>
    </div>
    <div class="d-flex align-items-center" style="gap:10px;">
      <span class="dash-cal-total-badge"><?= $grandTotal ?> 件</span>
      <?= $this->Html->link('<i class="fas fa-external-link-alt mr-1"></i>一覧', $listUrl, ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
    </div>
  </div>

  <div class="table-responsive">
    <table class="dash-cal-grid" style="min-width: 800px;">
      <thead>
        <tr>
          <?php foreach ($weekLabels as $wi => $label): ?>
            <th class="<?= $wi === 5 ? 'col-sat' : ($wi === 6 ? 'col-sun' : '') ?>"><?= h($label) ?></th>
          <?php endforeach; ?>
          <th style="width:104px;background:#f8f9fa;color:#5f6368;font-size:.78rem;font-weight:700;letter-spacing:.04em;">週計</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($calendarWeeks as $week): ?>
          <?php
            $weekTotal = 0;
            $weekAssignees = [];
            $weekDays = array_filter($week, fn($d) => $d !== null);
            $weekFrom = $weekDays ? sprintf('%s-%s-%02d', $yearStr, $monStr, min($weekDays)) : '';
            $weekTo   = $weekDays ? sprintf('%s-%s-%02d', $yearStr, $monStr, max($weekDays)) : '';
            foreach ($week as $wd) {
                if ($wd === null) continue;
                $wdInfo = $calData['byDay'][$wd] ?? ['total' => 0, 'assignees' => []];
                $weekTotal += (int)$wdInfo['total'];
                foreach ((array)$wdInfo['assignees'] as $a) {
                    $aName = (string)$a['name'];
                    if (!isset($weekAssignees[$aName])) {
                        $weekAssignees[$aName] = ['count' => 0, 'userId' => (int)($a['userId'] ?? 0)];
                    }
                    $weekAssignees[$aName]['count'] += (int)$a['count'];
                }
            }
            arsort($weekAssignees);

            // 週リンクURL
            $weekDayListUrl = $listUrl;
            if ($dateFromParam !== '' && $weekFrom !== '') {
                $weekDayListUrl .= '?' . $dateFromParam . '=' . urlencode($weekFrom) . '&' . $dateToParam . '=' . urlencode($weekTo);
            }
          ?>
          <tr>
            <?php foreach ($week as $wi => $day): ?>
              <?php
                $isSat   = ($wi === 5);
                $isSun   = ($wi === 6);
                $isToday = ($day !== null && $day === $todayDay);
                $tdClass = $day === null ? 'day-empty' : ($isToday ? 'day-today' : ($isSat ? 'col-sat' : ($isSun ? 'col-sun' : '')));
              ?>
              <td class="<?= $tdClass ?>">
                <?php if ($day !== null): ?>
                  <?php
                    $dayInfo = $calData['byDay'][$day] ?? ['total' => 0, 'assignees' => []];
                    $hasData = (int)$dayInfo['total'] > 0;
                    $dateStr = sprintf('%s-%s-%02d', $yearStr, $monStr, $day);
                    $numClass = 'day-num' . ($isToday ? ' today' : ($isSat ? ' sat' : ($isSun ? ' sun' : '')));
                    // 日付バッジリンク
                    $dayListUrl = $listUrl;
                    if ($dateFromParam !== '') {
                        $dayListUrl .= '?' . $dateFromParam . '=' . urlencode($dateStr) . '&' . $dateToParam . '=' . urlencode($dateStr);
                    }
                  ?>
                  <div class="day-num-wrap">
                    <span class="<?= $numClass ?>"><?= (int)$day ?></span>
                    <?php if ($hasData): ?>
                      <a href="<?= h($dayListUrl) ?>" class="day-count-link"><?= (int)$dayInfo['total'] ?></a>
                    <?php endif; ?>
                  </div>
                  <div class="assignee-pills">
                    <?php foreach ((array)$dayInfo['assignees'] as $assignee):
                      $color  = $badgeColor[(string)$assignee['name']] ?? '#6c757d';
                      $uid    = (int)($assignee['userId'] ?? 0);
                      $url    = $dayListUrl . ($uid > 0 ? (strpos($dayListUrl, '?') !== false ? '&' : '?') . $userParamName . '=' . $uid : '');
                    ?>
                      <a href="<?= h($url) ?>" class="assignee-pill" style="background:<?= h($color) ?>;">
                        <span><?= h((string)$assignee['name']) ?></span>
                        <span class="pill-cnt"><?= (int)$assignee['count'] ?></span>
                      </a>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </td>
            <?php endforeach; ?>
            <td class="col-week-sum">
              <?php if ($weekTotal > 0): ?>
                <a href="<?= h($weekDayListUrl) ?>" class="week-sum-cnt"><?= $weekTotal ?>件</a>
              <?php else: ?>
                <span class="week-sum-cnt" style="background:#ced4da;">0件</span>
              <?php endif; ?>
              <div class="assignee-pills">
                <?php foreach ($weekAssignees as $aName => $aInfo):
                  $wColor = $badgeColor[$aName] ?? '#6c757d';
                  $wUid   = (int)$aInfo['userId'];
                  $wUrl   = $weekDayListUrl . ($wUid > 0 ? (strpos($weekDayListUrl, '?') !== false ? '&' : '?') . $userParamName . '=' . $wUid : '');
                ?>
                  <a href="<?= h($wUrl) ?>" class="assignee-pill" style="background:<?= h($wColor) ?>;">
                    <span><?= h($aName) ?></span>
                    <span class="pill-cnt"><?= (int)$aInfo['count'] ?></span>
                  </a>
                <?php endforeach; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if (!empty($calData['assigneeTotals'])): ?>
  <div class="dash-legend">
    <span class="dash-legend-label">担当者</span>
    <?php foreach ((array)$calData['assigneeTotals'] as $assignee):
      $tColor = $badgeColor[(string)$assignee['name']] ?? '#6c757d';
      $uid = (int)($assignee['userId'] ?? 0);
      $legendUrl = $listUrl . ($uid > 0 ? '?' . $userParamName . '=' . $uid : '');
    ?>
      <a href="<?= h($legendUrl) ?>" class="legend-chip" style="text-decoration:none;color:#3c4043;">
        <span class="legend-dot" style="background:<?= h($tColor) ?>;"></span>
        <?= h((string)$assignee['name']) ?>
        <strong style="color:<?= h($tColor) ?>;"><?= (int)$assignee['count'] ?></strong>
      </a>
    <?php endforeach; ?>
    <span class="legend-total">合計 <?= $grandTotal ?> 件</span>
  </div>
  <?php endif; ?>

</div>
