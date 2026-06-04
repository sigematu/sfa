<?php
    $ranks = [
        '1' => __('S(100byen-)'),
        '2' => __('A(30-100byen)'),
        '3' => __('B(10-30byen)'),
        '4' => __('C(3-10byen)'),
        '5' => __('D(-3byen)')
    ];
    echo $this->Form->control('sales_rank', [
        'type' => 'select',
        'label' => __('Sales Rank'),
        'options' => $ranks
    ]);
?>
