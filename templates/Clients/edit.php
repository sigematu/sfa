<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Client $client
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
          <?= $this->Form->control('group_name', ['type' => 'text', 'label' => __('グループ'), 'placeholder' => __('グループ名')]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?= $this->Form->control('note', ['placeholder' => __('2025/3/1&#10;Change Corporation Name(Sample->Sample2)'), 'escape' => false]); ?>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col">
          <label class="font-weight-bold d-block mb-2"><?= __('Mail Recipients') ?></label>
          <small class="text-muted d-block mb-2"><?= __('You can specify up to 5 mail recipients.') ?></small>
          <table class="table table-sm">
            <thead>
              <tr>
                <th style="width: 40%;"><?= __('Email') ?></th>
                <th style="width: 40%;"><?= __('Department or Role') ?></th>
                <th style="width: 20%;" class="text-center"><?= __('Delivery') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i = 1; $i <= 5; $i++) : ?>
              <tr>
                <td>
                  <?= $this->Form->control("mail_email_{$i}", [
                      'type' => 'email',
                      'label' => false,
                      'placeholder' => __('sample@example.com'),
                  ]); ?>
                </td>
                <td>
                  <?= $this->Form->control("mail_dept_{$i}", [
                      'type' => 'text',
                      'label' => false,
                      'placeholder' => __('Sales Department / Manager'),
                  ]); ?>
                </td>
                <td class="text-center align-middle">
                  <?= $this->Form->hidden("mail_flag_{$i}", ['value' => 0]); ?>
                  <div class="custom-control custom-switch d-inline-block">
                    <?= $this->Form->checkbox("mail_flag_{$i}", [
                        'id' => "mail-flag-{$i}-toggle",
                        'class' => 'custom-control-input',
                        'value' => 1,
                        'required' => false,
                        'hiddenField' => false,
                        'checked' => (int)($client->{"mail_flag_{$i}"} ?? 0) === 1,
                    ]); ?>
                    <label class="custom-control-label" for="<?= "mail-flag-{$i}-toggle" ?>"><?= __('ON/OFF') ?></label>
                  </div>
                </td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
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

