<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClientProposal $clientProposal
 * @var \App\Model\Entity\ClientContact|null $matchedContact
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
        <th><?= __('Sender') ?></th>
        <td><?= h((string)$clientProposal->sender) ?></td>
      </tr>
      <tr>
        <th><?= __('Recipient') ?></th>
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
            <?= h((string)$clientProposal->recipient) ?>
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
