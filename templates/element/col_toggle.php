<?php
// Required: $colToggleKey (string), $colDefs (array col_key => label)
// Optional: $alwaysCols (array of always-visible col keys)
$alwaysCols = $alwaysCols ?? [];
echo $this->Html->script('col-toggle', ['once' => true]);
?>
<div style="position:relative; display:inline-block;">
    <button class="btn btn-sm btn-outline-secondary" type="button" id="colToggleBtn">
        <i class="fas fa-columns"></i> 列設定
    </button>
    <div id="colTogglePanel" class="shadow border bg-white rounded p-2"
        style="display:none; position:absolute; right:0; top:calc(100% + 4px); z-index:1050; min-width:160px;">
        <div class="px-2 pb-1 border-bottom mb-1">
            <button type="button" class="btn btn-xs btn-outline-secondary w-100" id="colResetBtn">リセット</button>
        </div>
        <?php foreach ($colDefs as $key => $label): ?>
            <?php if (in_array($key, $alwaysCols)): ?>
                <div class="px-2 py-1 text-muted small"><?= h($label) ?></div>
            <?php else: ?>
                <label class="d-flex align-items-center px-2 py-1 mb-0" style="cursor:pointer; gap:6px; white-space:nowrap;">
                    <input type="checkbox" class="js-col-toggle" data-col="<?= h($key) ?>" checked>
                    <span class="small"><?= h($label) ?></span>
                </label>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    initColToggle(<?= json_encode($colToggleKey) ?>, <?= json_encode($alwaysCols) ?>);
});
</script>
