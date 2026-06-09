<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Engineer $engineer
 */
?>
<?php
$this->assign('title', __('Add Engineer'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Engineers'), 'url' => ['action' => 'index']],
    ['title' => __('Add')],
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
            echo $this->Form->control('belong', ['type' => 'select', 'label' => __('Belong'), 'options' => $belongs, 'default' => BELONG_OUR]);
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
                <label class="custom-file-label" for="skill-sheet" data-browse="<?= __('Browse') ?>"><?= __('Choose file') ?></label>
              </div>
            </div>
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

<script>
document.getElementById('skill-sheet').addEventListener('change', function(e) {
    var fileName = e.target.files[0].name;
    var label = e.target.nextElementSibling;
    label.innerText = fileName;
});
</script>
