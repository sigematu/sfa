<?php
$currentStatus = (string)($this->request->getQuery('status') ?? '');
$isAllSelected = $currentStatus === '';
$isActiveSelected = $currentStatus === '1';
$isInactiveSelected = $currentStatus === '0';
?>
<label class="font-weight-bold d-block mb-2"><?= __('Status') ?></label>
<div class="btn-group btn-group-toggle d-flex flex-wrap" data-toggle="buttons">
    <label class="btn btn-outline-secondary flex-fill <?= $isAllSelected ? 'active' : '' ?>">
        <input type="radio" name="status" value="" autocomplete="off" <?= $isAllSelected ? 'checked' : '' ?>> すべて
    </label>
    <label class="btn btn-outline-success flex-fill <?= $isActiveSelected ? 'active' : '' ?>">
        <input type="radio" name="status" value="1" autocomplete="off" <?= $isActiveSelected ? 'checked' : '' ?>> <?= __('Active') ?>
    </label>
    <label class="btn btn-outline-secondary flex-fill <?= $isInactiveSelected ? 'active' : '' ?>">
        <input type="radio" name="status" value="0" autocomplete="off" <?= $isInactiveSelected ? 'checked' : '' ?>> <?= __('Inactive') ?>
    </label>
</div>
