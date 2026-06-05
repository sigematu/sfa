<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Bp $bp
 * @var string[] $locations
 * @var string[] $bpCategories
 */
?>
<?php
$this->assign('title', __('Add Bp'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Bps'), 'url' => ['action' => 'index']],
    ['title' => __('Add')],
]);
?>

<div class="card card-primary card-outline">
  <?= $this->Form->create($bp) ?>
  <div class="card-body">
    <div class="container">
      <div class="row">
        <div class="col">
          <?= $this->Form->control('name', ['label' => __('Company'), 'placeholder' => __('Sample Corporation')]); ?>
        </div>
        <div class="col">
          <?= $this->Form->control('kana', ['label' => __('Company Kana'), 'placeholder' => __('Sample')]); ?>
        </div>
        <div class="col">
          <?= $this->Form->control('url', ['placeholder' => __('https://www.sample.com/')]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->Form->control('note', ['placeholder' => __('2025/3/1&#10;Change Corporation Name(Sample->Sample2)'), 'escape' => false]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->Form->control('invoice_number', ['label' => __('Invoice Number'), 'placeholder' => __('13 digits excluding T')]); ?>
        </div>
        <div class="col">
          <?= $this->Form->control('location', ['type' => 'select', 'label' => __('所在地'), 'options' => $locations, 'empty' => __('選択してください')]); ?>
        </div>
        <div class="col">
          <?= $this->element('parts/status_d'); ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->Form->control('categories', ['label' => __('区分'), 'multiple' => 'checkbox', 'options' => $bpCategories]); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card-footer d-flex">
    <div class="ml-auto">
      <?= $this->Form->button(__('Save')) ?>
      <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>

  <?= $this->Form->hidden('created_id', ['value' => $this->request->getSession()->read('Auth.id')]); ?>
  <?= $this->Form->end() ?>
</div>

