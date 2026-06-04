<?php
    $payment_sites = [
        '30' => '30' . __('Days'),
        '40' => '40' . __('Days'),
        '50' => '50' . __('Days'),
        '60' => '60' . __('Days')
    ];
    echo $this->Form->control('payment_site', [
        'type' => 'select',
        'label' => __('Payment Site'),
        'options' => $payment_sites,
        'default' => 30,
    ]);
?>
