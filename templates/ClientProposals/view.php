<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClientProposal $clientProposal
 * @var \App\Model\Entity\ClientContact|null $matchedContact
 * @var \Cake\Datasource\EntityInterface|null $senderUser
 */
?>
<?php
$this->assign('title', __('Client Proposal'));
$this->Breadcrumbs->add([
    ['title' => __('Home'), 'url' => '/'],
    ['title' => __('List Client Proposals'), 'url' => ['action' => 'index']],
    ['title' => __('View')],
]);

$bodyText = (string)$clientProposal->body_text;
if ($bodyText === '' && !empty($clientProposal->body_html)) {
    $bodyText = strip_tags((string)$clientProposal->body_html);
}
?>

<div class="view card card-primary card-outline">
  <div class="card-header d-sm-flex">
    <h2 class="card-title"><?= h((string)$clientProposal->subject) ?></h2>
  </div>

  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <tr>
        <th><?= __('Date Time') ?></th>
        <td><?= h($clientProposal->received_at) ?></td>
      </tr>
      <tr>
        <th><?= __('営業状況') ?></th>
        <td><?= h($salesStatusLabels[(int)($clientProposal->sales_status ?? 0)] ?? '') ?></td>
      </tr>
      <tr>
        <th><?= __('事由') ?></th>
        <td><?= h($salesReasonLabels[(int)($clientProposal->sales_reason ?? 0)] ?? '') ?></td>
      </tr>
      <tr>
        <th><?= __('営業') ?></th>
        <td>
          <?php
            $senderRaw = (string)$clientProposal->sender;
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
        <th><?= __('顧客') ?></th>
        <td>
          <?php if (!empty($matchedContact)): ?>
            <?php if ($matchedContact->has('client')): ?>
              <?= $this->Html->link(
                  str_replace(['株式会社', '合同会社'], '', (string)$matchedContact->client->name),
                  ['controller' => 'Clients', 'action' => 'view', $matchedContact->client->id]
              ) ?>
            <?php else: ?>
              <?= __('Client') ?>
            <?php endif; ?>
            /
            <?= $this->Html->link(
                (string)$matchedContact->name,
                ['controller' => 'ClientContacts', 'action' => 'view', $matchedContact->id]
            ) ?>
          <?php else: ?>
            <?php
              $rawRecipient = (string)$clientProposal->recipient;
              preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $rawRecipient, $recipientMatch);
              $recipientEmail = strtolower((string)($recipientMatch[0] ?? ''));
            ?>
            <?= h($rawRecipient) ?>
            <div class="mt-2">
              <?= $this->Html->link(
                  __('Add Client'),
                  ['controller' => 'Clients', 'action' => 'add'],
                  ['class' => 'btn btn-xs btn-outline-secondary mr-1']
              ) ?>
              <?= $this->Html->link(
                  __('Add Client Contact'),
                  ['controller' => 'ClientContacts', 'action' => 'add', '?' => ['email' => $recipientEmail]],
                  ['class' => 'btn btn-xs btn-outline-secondary']
              ) ?>
            </div>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <th><?= __('Subject') ?></th>
        <td><?= h((string)$clientProposal->subject) ?></td>
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
