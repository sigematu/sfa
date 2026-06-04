<?php
    echo $this->Form->button(__('Search'), ['type' => 'submit', 'class' => 'btn btn-primary']);
    echo "&nbsp;";

    $url = $this->Url->build(['action' => 'index']);                            
    echo $this->Form->button(__('Reset'), ['type' => 'button', 'onClick' => 'location.href="' . $url . '"']);
    // echo $this->Html->link(__('Reset'), ['action' => 'index']);
    // if ($this->Search->isSearch()) {
    //     echo $this->Search->resetLink(__('Reset'), ['class' => 'button']);
    // }

    echo $this->Form->end();
?>
