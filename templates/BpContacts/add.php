<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BpContact $bpContact
 */
?>
<?php
$this->assign('title', __('Add Bp Contact'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Bp Contacts'), 'url' => ['action' => 'index']],
    ['title' => __('Add')],
]);
?>

<div class="card card-primary card-outline">
  <?= $this->Form->create($bpContact) ?>
  <div class="card-body">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <?php
            // クエリパラメータに bp_id がある場合はその値を固定（変更不可にしたい場合）
            // ない場合はドロップダウンで選択可能にする
            $sanitizedBps = [];
            foreach ($bps as $id => $name) {
                $sanitizedBps[$id] = str_replace(['株式会社', '合同会社'], '', $name);
            }
            $queryBpId = $this->request->getQuery('bp_id');
            echo $this->Form->control('bp_id', [
                'options' => $sanitizedBps,
                'label' => __('Bp'),
                'default' => $queryBpId,
                'empty' => __('Select Company') // 選択を促す空の選択肢
            ]);
          ?>
        </div>
        <div class="col-md-4">
          <?= $this->Form->control('name', ['label' => __('Bp Contact'), 'placeholder' => __('Taro Akiba')]); ?>
        </div>
        <div class="col-md-4">
          <?= $this->Form->control('kana', ['label' => __('Bp Contact Kana'), 'placeholder' => __('Taro Akiba kana')]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <?= $this->Form->control('email', ['label' => __('Email'), 'placeholder' => __('akiba@icz.co.jp')]); ?>
        </div>
        <div class="col-md-3">
          <?= $this->Form->control('mobile_phone', ['label' => __('Mobile'), 'placeholder' => __('090-1234-5678')]); ?>
        </div>
        <div class="col-md-3">
          <?= $this->Form->control('landline_phone', ['label' => __('Landline'), 'placeholder' => __('03-1234-5678')]); ?>
        </div>
        <div class="col-md-3">
          <?= $this->element('parts/position_e'); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <?= $this->Form->control('note', ['type' => 'textarea', 'label' => __('Note')]); ?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3">
          <?= $this->Form->control('status', [
              'type' => 'select',
              'options' => [1 => __('Active'), 0 => __('Inactive')],
              'default' => 1
          ]); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card-footer d-flex">
    <div class="ml-auto">
      <?= $this->Form->button(__('Save')) ?>
      <?= $this->Html->link(__('Cancel'), ['controller' => 'BpContacts', 'action' => 'index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>

  <?= $this->Form->hidden('created_id', ['value' => $this->request->getSession()->read('Auth.id')]); ?>
  
  <?= $this->Form->end() ?>
</div>
