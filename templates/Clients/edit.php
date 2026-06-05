<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Client $client
 * @var string[]|\Cake\Collection\CollectionInterface $groupClients
 */
?>
<?php
$this->assign('title', __('Edit Client'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Clients'), 'url' => ['action' => 'index']],
    ['title' => __('View'), 'url' => ['action' => 'view', $client->id]],
    ['title' => __('Edit')],
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
          <?= $this->element('parts/sales_rank'); ?>
        </div>
        <div class="col">
          <label class="font-weight-bold d-block mb-2"><?= __('親会社として設定') ?></label>
          <div class="border rounded p-2 bg-light">
            <div class="custom-control custom-switch">
              <?= $this->Form->checkbox('is_group', [
                  'id' => 'is-group-toggle',
                  'class' => 'custom-control-input',
                  'hiddenField' => true,
              ]); ?>
              <label class="custom-control-label" for="is-group-toggle"><?= __('この会社を親会社として扱う') ?></label>
            </div>
            <small class="text-muted d-block mt-2"><?= __('ONにすると、この会社は他社の「親会社」候補に表示されます。') ?></small>
          </div>
        </div>
        <div class="col">
          <?= $this->Form->control('parent_id', ['label' => __('親会社'), 'options' => $groupClients, 'empty' => __('親会社なし')]); ?>
          <small class="text-muted d-block mt-1"><?= __('必要な場合のみ選択します。') ?></small>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->Form->control('note', ['placeholder' => __('2025/3/1&#10;Change Corporation Name(Sample->Sample2)'), 'escape' => false]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->element('parts/status'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card-footer d-flex">
    <div class="">
      <?= $this->Form->postLink(
          __('Delete'),
          ['action' => 'delete', $client->id],
          ['block' => true, 'confirm' => __('Are you sure you want to delete # {0}?', $client->id), 'class' => 'btn btn-danger']
      ) ?>
    </div>
    <div class="ml-auto">
      <?= $this->Form->button(__('Save')) ?>
      <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>

  <?= $this->Form->hidden('modified_id', ['value' => $this->request->getSession()->read('Auth.id')]); ?>
  <?= $this->Form->end() ?>
  <?= $this->fetch('postLink') ?>
</div>

