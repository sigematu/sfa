<?php
    echo $this->Form->control('role', [
        'label' => __('Role'),
        'type' => 'select',
        'options' => CLIENT_CONTACT_ROLE_LABELS,
        'empty' => true,
    ]);
?>