<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BpProcurement $bpProcurement
 * @var \App\Model\Entity\BpContact|null $matchedContact
 * @var \Cake\Datasource\EntityInterface|null $senderUser
 */
?>
<?php
$this->assign('title', __('BP Procurement'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List BP Procurements'), 'url' => ['action' => 'index']],
    ['title' => __('View')],
]);

$bodyText = (string)$bpProcurement->body_text;
if ($bodyText === '' && !empty($bpProcurement->body_html)) {
    $bodyText = strip_tags((string)$bpProcurement->body_html);
}
?>

<div class="view card card-primary card-outline">
  <div class="card-header d-sm-flex">
    <h2 class="card-title"><?= h((string)$bpProcurement->subject) ?></h2>
  </div>

  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <tr>
        <th><?= __('Date Time') ?></th>
        <td><?= h($bpProcurement->received_at) ?></td>
      </tr>
      <tr>
        <th><?= __('営業状況') ?></th>
        <td><?= h($salesStatusLabels[(int)($bpProcurement->sales_status ?? 0)] ?? '') ?></td>
      </tr>
      <tr>
        <th><?= __('事由') ?></th>
        <td><?= h($salesReasonLabels[(int)($bpProcurement->sales_reason ?? 0)] ?? '') ?></td>
      </tr>
      <tr>
        <th><?= __('BP担当') ?></th>
        <td>
          <?php
            $senderRaw = (string)$bpProcurement->sender;
            $senderLabel = $senderRaw;
            if (!empty($senderUser)) {
              $senderName = (string)($senderUser->display_name ?? '');
              if ($senderName === '') {
                $senderName = (string)($senderUser->username ?? '');
              }
              $senderLabel = $senderName !== '' ? $senderName : $senderRaw;
            }
          ?>
          <?= h($senderLabel) ?>
        </td>
      </tr>
      <tr>
        <th><?= __('Bp') ?></th>
        <td>
          <?php if (!empty($matchedContact)): ?>
            <?php if ($matchedContact->has('bp')): ?>
              <?= $this->Html->link(
                  str_replace(['株式会社', '合同会社'], '', (string)$matchedContact->bp->name),
                  ['controller' => 'Bps', 'action' => 'view', $matchedContact->bp->id]
              ) ?>
            <?php else: ?>
              <?= __('Bp') ?>
            <?php endif; ?>
            /
            <?= $this->Html->link(
                (string)$matchedContact->name,
                ['controller' => 'BpContacts', 'action' => 'view', $matchedContact->id]
            ) ?>
          <?php else: ?>
            <?php
              $rawRecipient = (string)$bpProcurement->recipient;
              preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $rawRecipient, $recipientMatch);
              $recipientEmail = strtolower((string)($recipientMatch[0] ?? ''));
            ?>
            <?= h($rawRecipient) ?>
            <div class="mt-2">
              <?= $this->Html->link(
                  __('Add Bp'),
                  ['controller' => 'Bps', 'action' => 'add'],
                  ['class' => 'btn btn-xs btn-outline-secondary mr-1']
              ) ?>
              <?= $this->Html->link(
                  __('Add Bp Contact'),
                  ['controller' => 'BpContacts', 'action' => 'add'],
                  ['class' => 'btn btn-xs btn-outline-secondary']
              ) ?>
            </div>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <th><?= __('Subject') ?></th>
        <td><?= h((string)$bpProcurement->subject) ?></td>
      </tr>
      <tr>
        <th><?= __('Body') ?></th>
        <td style="white-space: pre-wrap;"><?= h($bodyText) ?></td>
      </tr>
    </table>
  </div>

  <div class="card-footer d-flex">
    <div class="ml-auto">
      <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>
</div>
