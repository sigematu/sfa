<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Bp $bp
 * @var string[] $locations
 * @var string[] $bpCategories
 */
?>
<?php
$this->assign('title', __('Edit Bp'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Bps'), 'url' => ['action' => 'index']],
    ['title' => __('View'), 'url' => ['action' => 'view', $bp->id]],
    ['title' => __('Edit')],
]);
?>

<style>
  .bp-category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 10px;
  }

  .bp-category-item {
    position: relative;
  }

  .bp-category-item input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }

  .bp-category-label {
    margin: 0;
    width: 100%;
    min-height: 44px;
    display: flex;
    align-items: center;
    padding: 10px 12px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    background: #f8fafc;
    color: #2d3748;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .bp-category-label:hover {
    border-color: #9db8df;
    background: #eef4fd;
  }

  .bp-category-item input[type="checkbox"]:checked + .bp-category-label {
    border-color: #0d6efd;
    background: #e7f1ff;
    color: #0a3f8a;
    box-shadow: inset 0 0 0 1px #0d6efd;
  }
</style>

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
          <?= $this->Form->control('categories', ['type' => 'hidden', 'value' => '']); ?>
          <label class="d-block mb-2"><?= __('区分') ?></label>
          <div class="bp-category-grid">
            <?php foreach ($bpCategories as $value => $label): ?>
              <div class="bp-category-item">
                <?= $this->Form->checkbox('categories[]', [
                    'value' => $value,
                    'checked' => in_array((string)$value, (array)$bp->categories, true),
                    'hiddenField' => false,
                    'id' => 'bp-category-' . $value,
                ]) ?>
                <?= $this->Form->label('bp-category-' . $value, $label, ['class' => 'bp-category-label']) ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->Form->control('location', ['type' => 'select', 'label' => __('所在地'), 'options' => $locations, 'empty' => __('選択してください')]); ?>
        </div>
        <div class="col">
          <?= $this->Form->control('invoice_number', ['label' => __('Invoice Number'), 'placeholder' => __('13 digits excluding T')]); ?>
        </div>
        <div class="col">
          <?= $this->element('parts/status'); ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->Form->control('note', ['placeholder' => __('2025/3/1&#10;Change Corporation Name(Sample->Sample2)'), 'escape' => false]); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card-footer d-flex">
    <div class="">
      <?= $this->Form->postLink(
          __('Delete'),
          ['action' => 'delete', $bp->id],
          ['block' => true, 'confirm' => __('Are you sure you want to delete # {0}?', $bp->id), 'class' => 'btn btn-danger']
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

