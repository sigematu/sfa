<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Engineer $engineer
 */
?>

<?php
$this->assign('title', __('Engineer'));
$this->Breadcrumbs->add([
  ['title' => __('Home'), 'url' => '/'],
  ['title' => __('List Engineers'), 'url' => ['action' => 'index']],
  ['title' => __('View')],
]);
?>

<div class="view card card-primary card-outline">
  <div class="card-header d-sm-flex">
    <h2 class="card-title"><?= h($engineer->name) ?></h2>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <tr>
        <th><?= __('Id') ?></th>
        <td><?= $this->Number->format($engineer->id) ?></td>
      </tr>
      <tr>
        <th><?= __('Employee No.') ?></th>
        <td><?= h($engineer->emp_no) ?></td>
      </tr>
      <tr>
        <th><?= __('Belong') ?></th>
        <td>
          <?php if ($engineer->belong === BELONG_OUR): ?>
            <?= __('Proper Staff') ?>
          <?php else: ?>
            <?= __('Bp Staff') ?>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <th><?= __('Name') ?></th>
        <td><?= h($engineer->name) ?></td>
      </tr>
      <tr>
        <th><?= __('Kana') ?></th>
        <td><?= h($engineer->kana) ?></td>
      </tr>
      <tr>
        <th><?= __('Birthyear') ?></th>
        <td>
          <?php
            if ($engineer->birthyear !== null) {
              $year = date('Y')-$engineer->birthyear;
              echo $year . __('years old');
              echo __(' (') . h($engineer->birthyear) . __('year') . ')';
            }
          ?>
        </td>
      </tr>
      <tr>
        <th><?= __('Year of Industry Experience') ?></th>
        <td><?= h($engineer->year_industory_exp) ?></td>
      </tr>
      <tr>
        <th><?= __('Skill Experience') ?></th>
        <td><?= h($engineer->skill_exp) ?></td>
      </tr>
      <tr>
        <th><?= __('Year of Skill Experience') ?></th>
        <td><?= h($engineer->year_skill_exp) ?></td>
      </tr>
      <tr>
        <th><?= __('Skill Sheet') ?></th>
        <td>
          <?php if (!empty($engineer->skill_sheet)): ?>
            <?= $this->Html->link($engineer->skill_sheet, ['action' => 'download', $engineer->id], ['target' => '_blank', 'class' => 'btn btn-xs btn-outline-info']) ?>
          <?php else: ?>
            <span class="text-muted"><?= __('No file uploaded') ?></span>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <th><?= __('Note') ?></th>
        <td><?= nl2br(h($engineer->note) ?? '') ?></td>
      </tr>
      <tr>
        <th><?= __('Status') ?></th>
        <td>
          <?php if ($engineer->status === STATUS_ACTIVE): ?>
            <?= __('Active') ?>
          <?php else: ?>
            <div class="text-muted"><?= __('Inactive') ?></div>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <th><?= __('Created User') ?></th>
        <td><?= h($engineer->created_dn) ?> <?= h($engineer->created_fn) ?></td>
      </tr>
      <tr>
        <th><?= __('Created') ?></th>
        <td><?= h($engineer->created) ?></td>
      </tr>
      <tr>
        <th><?= __('Modified User') ?></th>
        <td><?= h($engineer->modified_dn) ?> <?= h($engineer->modified_fn) ?></td>
      </tr>
      <tr>
      <tr>
        <th><?= __('Modified') ?></th>
        <td><?= h($engineer->modified) ?></td>
      </tr>
    </table>
  </div>
  <div class="card-footer d-flex">
    <div class="">
      <?= $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $engineer->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $engineer->id), 'class' => 'btn btn-danger']
      ) ?>
    </div>
    <div class="ml-auto">
      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $engineer->id], ['class' => 'btn btn-secondary']) ?>
      <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>
</div>
