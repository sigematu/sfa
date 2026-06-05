<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClientContact $clientContact
 * @var string[]|\Cake\Collection\CollectionInterface $clients
 * @var array<int, string> $categories
 * @var array<int, array<int, string>> $hierarchyOptionsByClient
 */
?>
<?php
$this->assign('title', __('Add Client Contact'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Client Contacts'), 'url' => ['action' => 'index']],
    ['title' => __('Add')],
]);

  $queryClientId = (int)$this->request->getQuery('client_id');
  $initialHierarchyOptions = $hierarchyOptionsByClient[$queryClientId] ?? [];
?>

<div class="card card-primary card-outline">
  <?= $this->Form->create($clientContact) ?>
  <div class="card-body">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <?php
            // クエリパラメータに client_id がある場合はその値を固定（変更不可にしたい場合）
            // ない場合はドロップダウンで選択可能にする
            $sanitizedClients = [];
            foreach ($clients as $id => $name) {
                $sanitizedClients[$id] = str_replace(['株式会社', '合同会社'], '', $name);
            }
            echo $this->Form->control('client_id', [
                'options' => $sanitizedClients,
                'label' => __('Client'),
                'default' => $queryClientId,
                'empty' => __('Select Company') // 選択を促す空の選択肢
            ]);
          ?>
        </div>
        <div class="col-md-4">
          <?= $this->Form->control('name', ['label' => __('Client Contact'), 'placeholder' => __('Taro Akiba')]); ?>
        </div>
        <div class="col-md-4">
          <?= $this->Form->control('kana', ['label' => __('Client Contact Kana'), 'placeholder' => __('Taro Akiba kana')]); ?>
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
      </div>
      <div class="row">
        <div class="col-md-3">
          <?= $this->Form->control('department', ['type' => 'text', 'label' => __('Department')]); ?>
        </div>
        <div class="col-md-3">
          <?= $this->element('parts/position_e'); ?>
        </div>
        <div class="col-md-3">
          <?= $this->Form->control('category', ['type' => 'select', 'label' => __('Mail Delivery Attribute'), 'options' => $categories, 'empty' => __('Select'), 'value' => $clientContact->category ?? CLIENT_CONTACT_CATEGORY_ALL]); ?>
        </div>
        <div class="col-md-3">
          <?= $this->Form->control('hierarchy', ['type' => 'select', 'label' => __('Hierarchy'), 'options' => $initialHierarchyOptions, 'empty' => __('Select')]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <?= $this->Form->control('note', ['type' => 'textarea', 'label' => __('Note')]); ?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3">
          <label class="font-weight-bold d-block mb-2"><?= __('Mail Delivery') ?></label>
          <?= $this->Form->hidden('mail_delivery', ['value' => 0]); ?>
          <div class="custom-control custom-switch mt-2">
            <?= $this->Form->checkbox('mail_delivery', [
                'id' => 'mail-delivery-toggle',
                'class' => 'custom-control-input',
                'value' => 1,
                'hiddenField' => false,
                'checked' => (int)($clientContact->mail_delivery ?? 1) === 1,
            ]); ?>
            <label class="custom-control-label" for="mail-delivery-toggle"><?= __('ON/OFF') ?></label>
          </div>
        </div>
        <div class="col-md-3">
          <label class="font-weight-bold d-block mb-2"><?= __('Deliver Responsible Area Only') ?></label>
          <?= $this->Form->hidden('area_only_delivery', ['value' => 0]); ?>
          <div class="custom-control custom-switch mt-2">
            <?= $this->Form->checkbox('area_only_delivery', [
                'id' => 'area-only-delivery-toggle',
                'class' => 'custom-control-input',
                'value' => 1,
                'hiddenField' => false,
                'checked' => (int)($clientContact->area_only_delivery ?? 0) === 1,
            ]); ?>
            <label class="custom-control-label" for="area-only-delivery-toggle"><?= __('ON/OFF') ?></label>
          </div>
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
      <?= $this->Html->link(__('Cancel'), ['controller' => 'ClientContacts', 'action' => 'index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>

  <?= $this->Form->hidden('created_id', ['value' => $this->request->getSession()->read('Auth.id')]); ?>
  
  <?= $this->Form->end() ?>
</div>

<?php $this->append('script'); ?>
<script>
  (function () {
    const hierarchyOptionsByClient = <?= json_encode($hierarchyOptionsByClient, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const clientSelect = document.getElementById('client-id');
    const hierarchySelect = document.getElementById('hierarchy');

    if (!clientSelect || !hierarchySelect) {
      return;
    }

    const initialHierarchy = hierarchySelect.value;

    function renderHierarchyOptions(selectedValue) {
      const clientId = clientSelect.value;
      const options = hierarchyOptionsByClient[clientId] || {};

      hierarchySelect.innerHTML = '';

      const emptyOption = document.createElement('option');
      emptyOption.value = '';
      emptyOption.textContent = 'Select';
      hierarchySelect.appendChild(emptyOption);

      Object.keys(options).forEach(function (id) {
        const option = document.createElement('option');
        option.value = id;
        option.textContent = options[id];
        hierarchySelect.appendChild(option);
      });

      if (selectedValue && options[selectedValue]) {
        hierarchySelect.value = selectedValue;
      } else {
        hierarchySelect.value = '';
      }
    }

    clientSelect.addEventListener('change', function () {
      renderHierarchyOptions('');
    });

    renderHierarchyOptions(initialHierarchy);
  })();
</script>
<?php $this->end(); ?>
