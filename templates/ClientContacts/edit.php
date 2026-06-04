<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClientContact $clientContact
 * @var string[]|\Cake\Collection\CollectionInterface $clients
 */
?>
<?php
$this->assign('title', __('Edit Client Contact'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Client Contacts'), 'url' => ['controller' => 'ClientContacts', 'action' => 'index']],
    ['title' => __('Edit')],
]);
?>

<div class="card card-primary card-outline">
  <?= $this->Form->create($clientContact) ?>
  <div class="card-body">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <?php
            $sanitizedClients = [];
            foreach ($clients as $id => $name) {
                $sanitizedClients[$id] = str_replace(['株式会社', '合同会社'], '', $name);
            }
            echo $this->Form->control('client_id', ['options' => $sanitizedClients, 'label' => __('Client')]);
          ?>
        </div>
        <div class="col-md-4">
          <?= $this->Form->control('name', ['label' => __('Client Contact')]); ?>
        </div>
        <div class="col-md-4">
          <?= $this->Form->control('kana', ['label' => __('Client Contact Kana')]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <?= $this->Form->control('email', ['label' => __('Email')]); ?>
        </div>
        <div class="col-md-3">
          <?= $this->Form->control('mobile_phone', ['label' => __('Mobile')]); ?>
        </div>
        <div class="col-md-3">
          <?= $this->Form->control('landline_phone', ['label' => __('Landline')]); ?>
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
              'label' => __('Status')
          ]); ?>
        </div>
      </div>

    </div>
  </div>

  <div class="card-footer d-flex">
    <div class="">
      <?= $this->Form->postLink(
          __('Delete'),
          ['action' => 'delete', $clientContact->id],
          ['block' => true, 'confirm' => __('Are you sure you want to delete # {0}?', $clientContact->id), 'class' => 'btn btn-danger']
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
