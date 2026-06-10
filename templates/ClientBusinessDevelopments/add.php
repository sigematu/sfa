<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClientBusinessDevelopment $record
 * @var array<int, string> $salesStatusLabels
 * @var array<int, string> $salesReasonLabels
 * @var array<int, string> $clientOptions
 * @var array<int, array{id:int,client_id:int,name:string}> $clientContactsData
 */
$isEdit = !$record->isNew();
$pageTitle = $isEdit ? __('顧客案件開拓 編集') : __('顧客案件開拓 追加');
$this->assign('title', $pageTitle);
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('顧客案件開拓'), 'url' => ['action' => 'index']],
    ['title' => $pageTitle],
]);
?>

<div class="card card-primary card-outline">
  <div class="card-header">
    <h2 class="card-title"><?= h($pageTitle) ?></h2>
  </div>
  <div class="card-body">
    <?= $this->Form->create($record) ?>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label"><?= __('日時') ?> <span class="text-danger">*</span></label>
      <div class="col-sm-4">
        <?= $this->Form->control('action_at', [
            'label' => false,
            'type' => 'datetime-local',
            'class' => 'form-control' . ($record->getError('action_at') ? ' is-invalid' : ''),
            'value' => $record->action_at ? $record->action_at->format('Y-m-d\TH:i') : date('Y-m-d\TH:i'),
        ]) ?>
        <?php if ($record->getError('action_at')): ?>
          <div class="invalid-feedback d-block"><?= h(implode(' ', (array)$record->getError('action_at'))) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label"><?= __('顧客') ?> <span class="text-danger">*</span></label>
      <div class="col-sm-4">
        <?= $this->Form->control('client_id', [
            'label' => false,
            'empty' => __('-- 顧客を選択 --'),
            'options' => $clientOptions,
            'class' => 'select2' . ($record->getError('client_id') ? ' is-invalid' : ''),
            'id' => 'cbd-client-id',
        ]) ?>
        <?php if ($record->getError('client_id')): ?>
          <div class="invalid-feedback d-block"><?= h(implode(' ', (array)$record->getError('client_id'))) ?></div>
        <?php endif; ?>
      </div>
      <label class="col-sm-2 col-form-label"><?= __('顧客担当者') ?></label>
      <div class="col-sm-4">
        <select name="client_contact_id" id="cbd-client-contact-id" class="form-control">
          <option value=""></option>
        </select>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label"><?= __('営業状況') ?> <span class="text-danger">*</span></label>
      <div class="col-sm-4">
        <?= $this->Form->control('sales_status', [
            'label' => false,
            'empty' => __('-- 選択 --'),
            'options' => $salesStatusLabels,
            'class' => 'select2' . ($record->getError('sales_status') ? ' is-invalid' : ''),
        ]) ?>
        <?php if ($record->getError('sales_status')): ?>
          <div class="invalid-feedback d-block"><?= h(implode(' ', (array)$record->getError('sales_status'))) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label"><?= __('事由') ?></label>
      <div class="col-sm-4">
        <?= $this->Form->control('sales_reason', [
            'label' => false,
            'options' => $salesReasonLabels,
            'class' => 'select2' . ($record->getError('sales_reason') ? ' is-invalid' : ''),
        ]) ?>
        <?php if ($record->getError('sales_reason')): ?>
          <div class="invalid-feedback d-block"><?= h(implode(' ', (array)$record->getError('sales_reason'))) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label"><?= __('状況') ?></label>
      <div class="col-sm-8">
        <?= $this->Form->control('status', [
            'label' => false,
            'type' => 'textarea',
            'rows' => 3,
            'class' => 'form-control' . ($record->getError('status') ? ' is-invalid' : ''),
        ]) ?>
        <?php if ($record->getError('status')): ?>
          <div class="invalid-feedback d-block"><?= h(implode(' ', (array)$record->getError('status'))) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-sm-offset-2 col-sm-10">
        <?= $this->Form->button($isEdit ? __('更新') : __('登録'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Html->link(__('キャンセル'), ['action' => 'index'], ['class' => 'btn btn-default ml-2']) ?>
      </div>
    </div>

    <?= $this->Form->end() ?>
  </div>
</div>

<?php
$clientContactsJson = json_encode(
    $clientContactsData,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
);
if ($clientContactsJson === false) {
    $clientContactsJson = '[]';
}
?>

<?php $this->append('lateScript'); ?>
<script>
(function ($) {
  var allContacts    = <?= $clientContactsJson ?>;
  var initialContact = '<?= h((string)($record->client_contact_id ?? '')) ?>';

  var $clientSelect  = $('#cbd-client-id');
  var $contactSelect = $('#cbd-client-contact-id');

  function initContactSelect2() {
    if ($contactSelect.hasClass('select2-hidden-accessible')) {
      $contactSelect.select2('destroy');
    }

    // destroy後に残るコンテナがあると二重表示になるため明示的に除去
    $contactSelect.siblings('span.select2').remove();

    $contactSelect.select2({
      language: 'ja',
      placeholder: '-- 顧客担当者を選択 --',
      allowClear: true,
      theme: 'bootstrap4',
    });
  }

  if (!Array.isArray(allContacts)) {
    allContacts = [];
  }

  function rebuildContacts(clientId, selectedId) {
    $contactSelect.empty().append('<option value=""></option>');

    if (clientId !== '') {
      allContacts.forEach(function (c) {
        if (String(c.client_id) === String(clientId)) {
          $contactSelect.append($('<option>').val(c.id).text(c.name));
        }
      });
    }

    initContactSelect2();
    $contactSelect.val(selectedId || null).trigger('change');
  }

  // グローバルSelect2初期化（document.ready）完了後に実行するためsetTimeoutで遅延
  $(document).ready(function () {
    setTimeout(function () {
      rebuildContacts($clientSelect.val() || '', initialContact);
      $clientSelect.on('select2:select', function () {
        rebuildContacts($(this).val() || '', '');
      });
      $clientSelect.on('select2:clear', function () {
        rebuildContacts('', '');
      });
    }, 0);
  });
}(jQuery));
</script>
<?php $this->end(); ?>
