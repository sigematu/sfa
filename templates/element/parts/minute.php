<?php
    $minutes = [
        '1' => __('1min'),
        '2' => __('5min'),
        '3' => __('10min'),
        '4' => __('15min'),
        '5' => __('30min')
    ];
    echo $this->Form->control('minute', [
        'type' => 'select',
        'label' => __('Minute'),
        'options' => $minutes
    ]);
?>
