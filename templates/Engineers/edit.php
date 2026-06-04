<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Engineer $engineer
 */
?>
<?php
$this->assign('title', __('Edit Engineer'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Engineers'), 'url' => ['action' => 'index']],
    ['title' => __('View'), 'url' => ['action' => 'view', $engineer->id]],
    ['title' => __('Edit')],
]);
?>

<div class="card card-primary card-outline">
  <?= $this->Form->create($engineer, ['type' => 'file']) ?>
  <div class="card-body">
  <div class="container">
      <div class="row">
        <div class="col">
          <?= $this->Form->control('emp_no', ['label' => __('Employee No.'), 'placeholder' => '202501']); ?>
        </div>
        <div class="col">
          <?php
            $belongs = ['1' => __('Proper Staff'), '2' => __('Bp Staff')];
            echo $this->Form->control('belong', ['type' => 'select', 'label' => __('Belong'), 'options' => $belongs]);
          ?>
        </div>
        <div class="col">
          <?= $this->Form->control('name', ['placeholder' => __('Taro Akiba')]); ?>
        </div>
        <div class="col">
          <?= $this->Form->control('kana', ['placeholder' => __('Taro Akiba kana')]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->Form->control('birthyear', ['placeholder' => 'yyyy']); ?>
        </div>
        <div class="col">
          <?= $this->Form->control('year_industory_exp', ['label' => __('Year of Industry Experience'), 'placeholder' => __('7 years')]); ?>
        </div>
        <div class="col">
          <?= $this->Form->control('skill_exp', ['label' => __('Skill Experience'), 'placeholder' => 'Java']); ?>
        </div>
        <div class="col">
          <?= $this->Form->control('year_skill_exp', ['label' => __('Year of Skill Experience'), 'placeholder' => __('3 years')]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="form-group">
            <label for="skill-sheet"><?= __('Skill Sheet') ?></label>
            <div class="input-group">
              <div class="custom-file">
                <?= $this->Form->file('skill_sheet', ['class' => 'custom-file-input', 'id' => 'skill-sheet']); ?>
                <label class="custom-file-label" for="skill-sheet" data-browse="<?= __('Browse') ?>"><?= !empty($engineer->skill_sheet) ? h($engineer->skill_sheet) : __('Choose file') ?></label>
              </div>
            </div>
            <?php if (!empty($engineer->skill_sheet)): ?>
              <small class="form-text text-muted">
                <?= __('Current File') ?>: <?= $this->Html->link($engineer->skill_sheet, ['action' => 'download', $engineer->id], ['target' => '_blank']) ?>
              </small>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->Form->control('note', ['placeholder' => __('2025/3/1&#10;Change Name(Akiba->Akiba2)'), 'escape' => false]); ?>
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
          ['action' => 'delete', $engineer->id],
          ['block' => true, 'confirm' => __('Are you sure you want to delete # {0}?', $engineer->id), 'class' => 'btn btn-danger']
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

<script>
document.getElementById('skill-sheet').addEventListener('change', function(e) {
    var fileName = e.target.files[0].name;
    var label = e.target.nextElementSibling;
    label.innerText = fileName;
});
</script>
