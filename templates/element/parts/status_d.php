<?php
$inputId = 'status-default-toggle';
?>
<label class="font-weight-bold d-block mb-2" for="<?= h($inputId) ?>"><?= __('Status') ?></label>
<div class="custom-control custom-switch">
    <?= $this->Form->checkbox('status', [
        'id' => $inputId,
        'value' => '1',
        'class' => 'custom-control-input',
        'checked' => true,
    ]); ?>
    <label class="custom-control-label" for="<?= h($inputId) ?>"><?= __('Active') ?></label>
</div>
<small class="text-muted d-block mt-1"><?= __('Toggle to switch between active and inactive.') ?></small>
