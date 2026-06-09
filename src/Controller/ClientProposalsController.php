<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\Pop3ClientProposalService;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * ClientProposals Controller
 *
 * @property \App\Model\Table\ClientProposalsTable $ClientProposals
 */
class ClientProposalsController extends AppController
{
    public $paginate = [
        'order' => ['ClientProposals.received_at' => 'DESC', 'ClientProposals.id' => 'DESC'],
        'limit' => 50,
    ];

    public function index()
    {
        if ($this->request->getQuery('sync') === '1') {
            $this->syncMailbox();

            return $this->redirect(['action' => 'index']);
        }

        $query = $this->ClientProposals->find();
        $searchKeyword = trim((string)$this->request->getQuery('q'));
        $searchStatus = $this->normalizeOptionValue($this->request->getQuery('sales_status'), CLIENT_PROPOSAL_SALES_STATUS_LABELS);
        $searchReason = $this->normalizeOptionValue($this->request->getQuery('sales_reason'), CLIENT_PROPOSAL_REASON_LABELS);
        $badgePeriod = trim((string)$this->request->getQuery('badge_period'));
        $badgeSender = trim((string)$this->request->getQuery('badge_sender'));
        $badgeBpPic = $this->normalizeSalesUserId($this->request->getQuery('badge_bp_pic'));

        if (in_array($badgePeriod, ['day', 'week', 'month'], true)) {
            $range = $this->resolveBadgePeriodRange($badgePeriod);
            $query->where([
                'ClientProposals.received_at >=' => $range['from'],
                'ClientProposals.received_at <=' => $range['to'],
            ]);
        }

        if ($badgeSender !== '') {
            $query->where(['ClientProposals.sender' => $badgeSender]);
        }
        if ($badgeBpPic !== null) {
            $query->where(['ClientProposals.bp_pic_id' => $badgeBpPic]);
        }

        if ($searchStatus !== null) {
            $query->where(['ClientProposals.sales_status' => $searchStatus]);
        }
        if ($searchReason !== null) {
            $query->where(['ClientProposals.sales_reason' => $searchReason]);
        }

        if ($searchKeyword !== '') {
            $query->where(function ($exp) use ($searchKeyword) {
                return $exp->or([
                    'ClientProposals.recipient LIKE' => '%' . $searchKeyword . '%',
                    'ClientProposals.subject LIKE' => '%' . $searchKeyword . '%',
                ]);
            });
        }

        $clientProposals = $this->paginate($query);

        $todayBadges = $this->buildSalesCountBadges(FrozenTime::now()->startOfDay(), FrozenTime::now()->endOfDay());
        $weeklyBadges = $this->buildSalesCountBadges(FrozenTime::now()->startOfWeek(), FrozenTime::now()->endOfWeek());
        $monthlyBadges = $this->buildSalesCountBadges(FrozenTime::now()->startOfMonth(), FrozenTime::now()->endOfMonth());
        $todayBpBadges = $this->buildBpPicCountBadges(FrozenTime::now()->startOfDay(), FrozenTime::now()->endOfDay());
        $weeklyBpBadges = $this->buildBpPicCountBadges(FrozenTime::now()->startOfWeek(), FrozenTime::now()->endOfWeek());
        $monthlyBpBadges = $this->buildBpPicCountBadges(FrozenTime::now()->startOfMonth(), FrozenTime::now()->endOfMonth());
        $monthlySalesStatusTabs = $this->buildMonthlySalesStatusTabs();

        $senderUserMap = $this->buildSenderUserMap($clientProposals);
        $salesUserOptions = $this->getSalesUserOptions();

        $recipientEmails = [];
        foreach ($clientProposals as $proposal) {
            foreach ($this->extractEmails((string)$proposal->recipient) as $email) {
                $recipientEmails[$email] = true;
            }
        }

        $contactByEmail = [];
        if (!empty($recipientEmails)) {
            $emails = array_keys($recipientEmails);
            $clientContactsTable = TableRegistry::getTableLocator()->get('ClientContacts');
            $contacts = $clientContactsTable->find()
                ->select(['id', 'client_id', 'name', 'email'])
                ->where(['ClientContacts.email IN' => $emails])
                ->contain(['Clients' => function ($q) {
                    return $q->select(['id', 'name']);
                }])
                ->all();

            foreach ($contacts as $contact) {
                $email = strtolower((string)$contact->email);
                if (!isset($contactByEmail[$email])) {
                    $contactByEmail[$email] = $contact;
                }
            }
        }

        $proposalContactMap = [];
        foreach ($clientProposals as $proposal) {
            foreach ($this->extractEmails((string)$proposal->recipient) as $email) {
                if (isset($contactByEmail[$email])) {
                    $proposalContactMap[$proposal->id] = $contactByEmail[$email];
                    break;
                }
            }
        }

        $salesStatusLabels = CLIENT_PROPOSAL_SALES_STATUS_LABELS;
        $salesReasonLabels = CLIENT_PROPOSAL_REASON_LABELS;
        $this->set(compact('clientProposals', 'proposalContactMap', 'salesStatusLabels', 'salesReasonLabels', 'senderUserMap', 'salesUserOptions', 'searchKeyword', 'searchStatus', 'searchReason', 'todayBadges', 'weeklyBadges', 'monthlyBadges', 'todayBpBadges', 'weeklyBpBadges', 'monthlyBpBadges', 'monthlySalesStatusTabs', 'badgePeriod', 'badgeSender', 'badgeBpPic'));
    }

    public function view($id = null)
    {
        $clientProposal = $this->ClientProposals->get($id);
        $matchedContact = $this->findContactByRecipient((string)$clientProposal->recipient);
        $senderUser = null;
        if (ctype_digit((string)$clientProposal->sender)) {
            $senderUser = TableRegistry::getTableLocator()->get('Users')->find()
                ->select(['id', 'display_name', 'username'])
                ->where(['id' => (int)$clientProposal->sender])
                ->first();
        }
        $salesStatusLabels = CLIENT_PROPOSAL_SALES_STATUS_LABELS;
        $salesReasonLabels = CLIENT_PROPOSAL_REASON_LABELS;

        $this->set(compact('clientProposal', 'matchedContact', 'salesStatusLabels', 'salesReasonLabels', 'senderUser'));
    }

    public function updateSalesResult()
    {
        $this->request->allowMethod(['post']);

        $id = (int)$this->request->getData('proposal_id');
        if ($id <= 0) {
            $this->Flash->error(__('Invalid client proposal.'));

            return $this->redirect(['action' => 'index']);
        }

        $clientProposal = $this->ClientProposals->get($id);
        $data = [];
        if ($this->request->getData('sales_status') !== null) {
            $data['sales_status'] = $this->normalizeOptionValue(
                $this->request->getData('sales_status'),
                CLIENT_PROPOSAL_SALES_STATUS_LABELS
            );
        }
        if ($this->request->getData('sales_reason') !== null) {
            $data['sales_reason'] = $this->normalizeOptionValue(
                $this->request->getData('sales_reason'),
                CLIENT_PROPOSAL_REASON_LABELS
            );
        }
        if ($this->request->getData('bp_pic_id') !== null) {
            $data['bp_pic_id'] = $this->normalizeSalesUserId($this->request->getData('bp_pic_id'));
        }

        if ($data === []) {
            $this->Flash->error(__('No update data was provided.'));

            return $this->redirect(['action' => 'index']);
        }

        $clientProposal = $this->ClientProposals->patchEntity($clientProposal, $data);
        if ($this->ClientProposals->save($clientProposal)) {
            $this->Flash->success(__('The client proposal has been saved.'));
        } else {
            $this->Flash->error(__('The client proposal could not be saved. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function syncMailbox(): void
    {
        $service = new Pop3ClientProposalService($this->ClientProposals, (array)Configure::read('Pop3ClientProposal', []));

        try {
            $count = $service->import();
            $this->Flash->success(__('Mail sync completed. {0} new message(s) imported.', $count));
        } catch (\Throwable $e) {
            Log::error('Client proposal POP3 sync failed: ' . $e->getMessage());
            $this->Flash->error($e->getMessage());
        }
    }

    /**
     * @return array<int, string>
     */
    private function extractEmails(string $recipient): array
    {
        if ($recipient === '') {
            return [];
        }

        preg_match_all('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $recipient, $matches);
        if (empty($matches[0])) {
            return [];
        }

        return array_values(array_unique(array_map('strtolower', $matches[0])));
    }

    private function findContactByRecipient(string $recipient)
    {
        $emails = $this->extractEmails($recipient);
        if (empty($emails)) {
            return null;
        }

        $clientContactsTable = TableRegistry::getTableLocator()->get('ClientContacts');

        return $clientContactsTable->find()
            ->select(['id', 'client_id', 'name', 'email'])
            ->where(['ClientContacts.email IN' => $emails])
            ->contain(['Clients' => function ($q) {
                return $q->select(['id', 'name']);
            }])
            ->first();
    }

    /**
     * @param iterable<\App\Model\Entity\ClientProposal> $clientProposals
     * @return array<int, \Cake\Datasource\EntityInterface>
     */
    private function buildSenderUserMap(iterable $clientProposals): array
    {
        $senderUserIds = [];
        foreach ($clientProposals as $proposal) {
            $sender = (string)$proposal->sender;
            if (ctype_digit($sender)) {
                $senderUserIds[(int)$sender] = true;
            }
        }

        if (empty($senderUserIds)) {
            return [];
        }

        $users = TableRegistry::getTableLocator()->get('Users')->find()
            ->select(['id', 'display_name', 'username'])
            ->where(['id IN' => array_keys($senderUserIds)])
            ->all();

        $map = [];
        foreach ($users as $user) {
            $map[(int)$user->id] = $user;
        }

        return $map;
    }

    /**
     * @param mixed $value
     * @param array<int, string> $labels
     */
    private function normalizeOptionValue($value, array $labels): ?int
    {
        $raw = trim((string)$value);
        if ($raw === '' || !ctype_digit($raw)) {
            return null;
        }

        $intValue = (int)$raw;

        return isset($labels[$intValue]) ? $intValue : null;
    }

    /**
     * @return array<int, string>
     */
    private function getSalesUserOptions(): array
    {
        $users = TableRegistry::getTableLocator()->get('Users')->find()
            ->select(['id', 'display_name', 'username', 'position'])
            ->where(['job' => JOB_SALES, 'active' => true])
            ->order(['position' => 'ASC', 'display_name' => 'ASC', 'username' => 'ASC'])
            ->all();

        $options = [];
        foreach ($users as $user) {
            $name = (string)($user->display_name ?? '');
            if ($name === '') {
                $name = (string)($user->username ?? '');
            }
            if ($name === '') {
                $name = (string)$user->id;
            }
            $options[(int)$user->id] = $name;
        }

        return $options;
    }

    /**
     * @param mixed $value
     */
    private function normalizeSalesUserId($value): ?int
    {
        $raw = trim((string)$value);
        if ($raw === '' || !ctype_digit($raw)) {
            return null;
        }

        $id = (int)$raw;
        $options = $this->getSalesUserOptions();

        return isset($options[$id]) ? $id : null;
    }

    /**
     * @return array<int, array{name:string,count:int,sender:string}>
     */
    private function buildSalesCountBadges(FrozenTime $from, FrozenTime $to): array
    {
        $query = $this->ClientProposals->find();
        $rows = $query
            ->select([
                'sender',
                'proposal_count' => $query->func()->count('id'),
            ])
            ->where([
                'received_at >=' => $from,
                'received_at <=' => $to,
                'sender <>' => '',
            ])
            ->group(['sender'])
            ->enableHydration(false)
            ->toArray();

        if (empty($rows)) {
            return [];
        }

        $senderIds = [];
        foreach ($rows as $row) {
            $sender = (string)($row['sender'] ?? '');
            if (ctype_digit($sender)) {
                $senderIds[(int)$sender] = true;
            }
        }

        $userMap = [];
        if (!empty($senderIds)) {
            $users = TableRegistry::getTableLocator()->get('Users')->find()
                ->select(['id', 'display_name', 'username'])
                ->where(['id IN' => array_keys($senderIds)])
                ->all();
            foreach ($users as $user) {
                $name = (string)($user->display_name ?? '');
                if ($name === '') {
                    $name = (string)($user->username ?? '');
                }
                $userMap[(int)$user->id] = $name;
            }
        }

        $badges = [];
        foreach ($rows as $row) {
            $sender = (string)($row['sender'] ?? '');
            $count = (int)($row['proposal_count'] ?? 0);
            if ($count <= 0) {
                continue;
            }

            $name = $sender;
            if (ctype_digit($sender) && isset($userMap[(int)$sender])) {
                $name = $userMap[(int)$sender];
            }

            $badges[] = [
                'name' => $name,
                'count' => $count,
                'sender' => $sender,
            ];
        }

        usort($badges, function (array $a, array $b): int {
            return $b['count'] <=> $a['count'];
        });

        return $badges;
    }

    /**
     * @return array<int, array{name:string,count:int,bp_pic_id:string}>
     */
    private function buildBpPicCountBadges(FrozenTime $from, FrozenTime $to): array
    {
        $query = $this->ClientProposals->find();
        $rows = $query
            ->select([
                'bp_pic_id',
                'proposal_count' => $query->func()->count('id'),
            ])
            ->where([
                'received_at >=' => $from,
                'received_at <=' => $to,
                'bp_pic_id IS NOT' => null,
            ])
            ->group(['bp_pic_id'])
            ->enableHydration(false)
            ->toArray();

        if (empty($rows)) {
            return [];
        }

        $userNames = $this->getSalesUserOptions();
        $badges = [];
        foreach ($rows as $row) {
            $bpPicId = (int)($row['bp_pic_id'] ?? 0);
            $count = (int)($row['proposal_count'] ?? 0);
            if ($bpPicId <= 0 || $count <= 0) {
                continue;
            }

            $name = $userNames[$bpPicId] ?? (string)$bpPicId;
            $badges[] = [
                'name' => $name,
                'count' => $count,
                'bp_pic_id' => (string)$bpPicId,
            ];
        }

        usort($badges, function (array $a, array $b): int {
            return $b['count'] <=> $a['count'];
        });

        return $badges;
    }

    /**
     * @return array<int, array{sender_id:int,name:string,total:int,rows:array<int, array{label:string,count:int,percentage:float}>}>
     */
    private function buildMonthlySalesStatusTabs(): array
    {
        $from = FrozenTime::now()->startOfMonth();
        $to = FrozenTime::now()->endOfMonth();

        $query = $this->ClientProposals->find();
        $rows = $query
            ->select([
                'sender',
                'sales_status',
                'proposal_count' => $query->func()->count('id'),
            ])
            ->where([
                'received_at >=' => $from,
                'received_at <=' => $to,
                'sender <>' => '',
            ])
            ->group(['sender', 'sales_status'])
            ->enableHydration(false)
            ->toArray();

        if (empty($rows)) {
            return [];
        }

        $senderIds = [];
        $statusCountBySender = [];
        foreach ($rows as $row) {
            $sender = (string)($row['sender'] ?? '');
            if (!ctype_digit($sender)) {
                continue;
            }

            $senderId = (int)$sender;
            $status = (int)($row['sales_status'] ?? 0);
            $count = (int)($row['proposal_count'] ?? 0);
            if ($count <= 0) {
                continue;
            }

            $senderIds[$senderId] = true;
            if (!isset($statusCountBySender[$senderId])) {
                $statusCountBySender[$senderId] = [];
            }
            if (!isset($statusCountBySender[$senderId][$status])) {
                $statusCountBySender[$senderId][$status] = 0;
            }
            $statusCountBySender[$senderId][$status] += $count;
        }

        if (empty($statusCountBySender)) {
            return [];
        }

        $nameMap = [];
        if (!empty($senderIds)) {
            $users = TableRegistry::getTableLocator()->get('Users')->find()
                ->select(['id', 'display_name', 'username'])
                ->where(['id IN' => array_keys($senderIds)])
                ->all();
            foreach ($users as $user) {
                $name = (string)($user->display_name ?? '');
                if ($name === '') {
                    $name = (string)($user->username ?? '');
                }
                $nameMap[(int)$user->id] = $name !== '' ? $name : (string)$user->id;
            }
        }

        $tabs = [];
        foreach ($statusCountBySender as $senderId => $statusCounts) {
            $total = array_sum($statusCounts);
            if ($total <= 0) {
                continue;
            }

            $detailRows = [];
            foreach (CLIENT_PROPOSAL_SALES_STATUS_LABELS as $statusValue => $statusLabel) {
                $count = (int)($statusCounts[(int)$statusValue] ?? 0);
                $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0.0;
                $detailRows[] = [
                    'label' => $statusLabel,
                    'count' => $count,
                    'percentage' => $percentage,
                ];
            }

            $tabs[] = [
                'sender_id' => $senderId,
                'name' => $nameMap[$senderId] ?? (string)$senderId,
                'total' => $total,
                'rows' => $detailRows,
            ];
        }

        usort($tabs, function (array $a, array $b): int {
            return $b['total'] <=> $a['total'];
        });

        return $tabs;
    }

    /**
     * @return array{from: \Cake\I18n\FrozenTime, to: \Cake\I18n\FrozenTime}
     */
    private function resolveBadgePeriodRange(string $period): array
    {
        $now = FrozenTime::now();

        if ($period === 'day') {
            return ['from' => $now->startOfDay(), 'to' => $now->endOfDay()];
        }
        if ($period === 'week') {
            return ['from' => $now->startOfWeek(), 'to' => $now->endOfWeek()];
        }

        return ['from' => $now->startOfMonth(), 'to' => $now->endOfMonth()];
    }
}
