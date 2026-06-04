<?php
    $statuses = ['0' => __('Inactive'), '1' => __('Active')];
    echo $this->Form->control('status', ['type' => 'select', 'label' => __('Status'), 'options' => $statuses]);
?>
