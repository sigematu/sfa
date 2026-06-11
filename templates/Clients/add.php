<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Client $client
 */
?>
<?php
$this->assign('title', __('Add Client'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Clients'), 'url' => ['action' => 'index']],
    ['title' => __('Add')],
]);
?>

<div class="card card-primary card-outline">
  <?= $this->Form->create($client) ?>
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
          <?= $this->element('parts/sales_rank_e'); ?>
        </div>
        <div class="col">
          <label class="d-block"><?= __('口座') ?></label>
          <?= $this->Form->hidden('account', ['value' => 0]); ?>
          <div class="custom-control custom-switch mt-2">
            <?= $this->Form->checkbox('account', [
                'id' => 'account-toggle',
                'class' => 'custom-control-input',
                'value' => 1,
                'required' => false,
                'hiddenField' => false,
                'checked' => (int)($client->account ?? 0) === 1,
            ]); ?>
            <label class="custom-control-label" for="account-toggle"><?= __('あり') ?> / <?= __('なし') ?></label>
          </div>
        </div>
        <div class="col">
          <?= $this->Form->control('group_name', ['type' => 'text', 'label' => __('グループ'), 'placeholder' => __('グループ名')]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->Form->control('note', ['placeholder' => __('2025/3/1&#10;Change Corporation Name(Sample->Sample2)'), 'escape' => false]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->element('parts/status_d'); ?>
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

