<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClientContact $clientContact
 * @var string[]|\Cake\Collection\CollectionInterface $clients
 * @var array<int, string> $categories
 * @var array<int, string> $inactiveReasons
 * @var array<int, array<int, string>> $hierarchyOptionsByClient
 */
?>
<?php
$this->assign('title', __('Edit Client Contact'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Client Contacts'), 'url' => ['controller' => 'ClientContacts', 'action' => 'index']],
    ['title' => __('Edit')],
]);

$initialHierarchyOptions = $hierarchyOptionsByClient[(int)($clientContact->client_id ?? 0)] ?? [];
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
            echo $this->Form->control('client_id', ['options' => $sanitizedClients, 'label' => __('Client'), 'class' => 'select2']);
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
      </div>
      <div class="row">
        <div class="col-md-3">
          <?= $this->Form->control('department', ['type' => 'text', 'label' => __('Department')]); ?>
        </div>
        <div class="col-md-3">
          <?= $this->element('parts/position_e'); ?>
        </div>
        <div class="col-md-3">
          <?= $this->Form->control('hierarchy', ['type' => 'select', 'label' => __('Hierarchy'), 'options' => $initialHierarchyOptions, 'empty' => __('Select'), 'class' => 'select2']); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <?= $this->Form->control('category', ['type' => 'select', 'label' => __('Responsible Area'), 'options' => $categories, 'empty' => __('Select'), 'value' => $clientContact->category ?? CLIENT_CONTACT_CATEGORY_ALL]); ?>
        </div>
        <div class="col-md-6">
          <?= $this->element('parts/role_e'); ?>
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
              'required' => false,
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
                'required' => false,
                'hiddenField' => false,
              'checked' => (int)($clientContact->area_only_delivery ?? 0) === 1,
            ]); ?>
            <label class="custom-control-label" for="area-only-delivery-toggle"><?= __('ON/OFF') ?></label>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <?= $this->Form->control('note', ['type' => 'textarea', 'label' => __('Note')]); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <label class="font-weight-bold d-block mb-2"><?= __('Status') ?></label>
          <?= $this->Form->hidden('status', ['value' => 0]); ?>
          <div class="custom-control custom-switch mt-2">
            <?= $this->Form->checkbox('status', [
                'id' => 'status-toggle',
                'class' => 'custom-control-input',
                'value' => 1,
              'required' => false,
                'hiddenField' => false,
                'checked' => (int)$clientContact->status === 1,
            ]); ?>
            <label class="custom-control-label" for="status-toggle"><?= __('ON/OFF') ?></label>
          </div>
        </div>
        <div class="col-md-3" id="inactive-reason-wrapper">
          <?= $this->Form->control('inactive_reason', ['type' => 'select', 'label' => __('Inactive Reason'), 'options' => $inactiveReasons, 'empty' => __('Select'), 'required' => false]); ?>
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

<?php $this->append('script'); ?>
<script>
  (function () {
    const hierarchyOptionsByClient = <?= json_encode($hierarchyOptionsByClient, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const clientSelect = document.getElementById('client-id');
    const hierarchySelect = document.getElementById('hierarchy');
    const statusToggle = document.getElementById('status-toggle');
    const inactiveReasonWrapper = document.getElementById('inactive-reason-wrapper');
    const inactiveReasonSelect = document.getElementById('inactive-reason');

    if (!clientSelect || !hierarchySelect) {
      return;
    }

    const initialHierarchy = hierarchySelect.value;

    function refreshHierarchySelect2() {
      if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2) {
        return;
      }

      const $hierarchy = window.jQuery(hierarchySelect);
      if ($hierarchy.hasClass('select2-hidden-accessible')) {
        $hierarchy.select2('destroy');
      }

      $hierarchy.select2({
        theme: 'bootstrap4',
        language: 'ja',
        width: '100%'
      });
    }

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

      refreshHierarchySelect2();
    }

    function toggleInactiveReason() {
      if (!statusToggle || !inactiveReasonWrapper || !inactiveReasonSelect) {
        return;
      }

      if (statusToggle.checked) {
        inactiveReasonWrapper.style.display = 'none';
        inactiveReasonSelect.value = '';
        inactiveReasonSelect.disabled = true;
      } else {
        inactiveReasonWrapper.style.display = '';
        inactiveReasonSelect.disabled = false;
      }
    }

    clientSelect.addEventListener('change', function () {
      renderHierarchyOptions('');
    });

    if (statusToggle) {
      statusToggle.addEventListener('change', toggleInactiveReason);
    }

    renderHierarchyOptions(initialHierarchy);
    toggleInactiveReason();
  })();
</script>
<?php $this->end(); ?>
