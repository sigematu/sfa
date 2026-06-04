<?php
    $fees = ['1' => __('Our Fee'), '2' => __('Other Fee')];
    echo $this->Form->control('fee', ['type' => 'select', 'label' => __('Fee'), 'options' => $fees, 'empty' => true]);
?>
