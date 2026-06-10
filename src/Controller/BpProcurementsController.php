<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\Pop3BpProcurementService;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * BpProcurements Controller
 *
 * @property \App\Model\Table\BpProcurementsTable $BpProcurements
 */
class BpProcurementsController extends AppController
{
    public $paginate = [
        'order' => ['BpProcurements.received_at' => 'DESC', 'BpProcurements.id' => 'DESC'],
        'limit' => 50,
    ];

    public function index()
    {
        if ($this->request->getQuery('sync') === '1') {
            $this->syncMailbox();

            return $this->redirect(['action' => 'index']);
        }

        $query = $this->BpProcurements->find();
        $searchKeyword = trim((string)$this->request->getQuery('q'));
        $searchStatus = $this->normalizeOptionValue($this->request->getQuery('sales_status'), BP_PROCUREMENT_STATUS_LABELS);
        $searchReason = $this->normalizeOptionValue($this->request->getQuery('sales_reason'), BP_PROCUREMENT_REASON_LABELS);
        $searchEvaluation = $this->normalizeOptionValue($this->request->getQuery('evaluation'), BP_PROCUREMENT_EVALUATION_LABELS);
        $badgePeriod = trim((string)$this->request->getQuery('badge_period'));
        $badgeSender = trim((string)$this->request->getQuery('badge_sender'));
        $dateFrom = trim((string)$this->request->getQuery('date_from'));
        $dateTo = trim((string)$this->request->getQuery('date_to'));

        if ($dateFrom !== '' && $dateTo !== '') {
            try {
                $query->where([
                    'BpProcurements.received_at >=' => new FrozenTime($dateFrom . ' 00:00:00'),
                    'BpProcurements.received_at <=' => new FrozenTime($dateTo . ' 23:59:59'),
                ]);
            } catch (\Throwable $e) {
                // invalid date — ignore
            }
        } elseif (in_array($badgePeriod, ['day', 'week', 'month'], true)) {
            $range = $this->resolveBadgePeriodRange($badgePeriod);
            $query->where([
                'BpProcurements.received_at >=' => $range['from'],
                'BpProcurements.received_at <=' => $range['to'],
            ]);
        }

        if ($badgeSender !== '') {
            $query->where(['BpProcurements.sender' => $badgeSender]);
        }

        if ($searchStatus !== null) {
            $query->where(['BpProcurements.sales_status' => $searchStatus]);
        }
        if ($searchReason !== null) {
            $query->where(['BpProcurements.sales_reason' => $searchReason]);
        }
        if ($searchEvaluation !== null) {
            $query->where(['BpProcurements.evaluation' => $searchEvaluation]);
        }

        if ($searchKeyword !== '') {
            $query->where(function ($exp) use ($searchKeyword) {
                return $exp->or([
                    'BpProcurements.recipient LIKE' => '%' . $searchKeyword . '%',
                    'BpProcurements.subject LIKE' => '%' . $searchKeyword . '%',
                ]);
            });
        }

        $bpProcurements = $this->paginate($query);

        $todayBadges = $this->buildSalesCountBadges(FrozenTime::now()->startOfDay(), FrozenTime::now()->endOfDay());
        $weeklyBadges = $this->buildSalesCountBadges(FrozenTime::now()->startOfWeek(), FrozenTime::now()->endOfWeek());
        $monthlyBadges = $this->buildSalesCountBadges(FrozenTime::now()->startOfMonth(), FrozenTime::now()->endOfMonth());

        $senderUserMap = $this->buildSenderUserMap($bpProcurements);

        $recipientEmails = [];
        foreach ($bpProcurements as $procurement) {
            foreach ($this->extractEmails((string)$procurement->recipient) as $email) {
                $recipientEmails[$email] = true;
            }
        }

        $contactByEmail = [];
        if (!empty($recipientEmails)) {
            $emails = array_keys($recipientEmails);
            $bpContactsTable = TableRegistry::getTableLocator()->get('BpContacts');
            $contacts = $bpContactsTable->find()
                ->select(['id', 'bp_id', 'name', 'email'])
                ->where(['BpContacts.email IN' => $emails])
                ->contain(['Bps' => function ($q) {
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

        $procurementContactMap = [];
        foreach ($bpProcurements as $procurement) {
            foreach ($this->extractEmails((string)$procurement->recipient) as $email) {
                if (isset($contactByEmail[$email])) {
                    $procurementContactMap[$procurement->id] = $contactByEmail[$email];
                    break;
                }
            }
        }

        $salesStatusLabels = BP_PROCUREMENT_STATUS_LABELS;
        $salesReasonLabels = BP_PROCUREMENT_REASON_LABELS;
        $evaluationLabels = BP_PROCUREMENT_EVALUATION_LABELS;
        $this->set(compact('bpProcurements', 'procurementContactMap', 'salesStatusLabels', 'salesReasonLabels', 'evaluationLabels', 'senderUserMap', 'searchKeyword', 'searchStatus', 'searchReason', 'searchEvaluation', 'todayBadges', 'weeklyBadges', 'monthlyBadges', 'badgePeriod', 'badgeSender', 'dateFrom', 'dateTo'));
    }

    public function view($id = null)
    {
        $bpProcurement = $this->BpProcurements->get($id);
        $matchedContact = $this->findContactByRecipient((string)$bpProcurement->recipient);
        $senderUser = null;
        if (ctype_digit((string)$bpProcurement->sender)) {
            $senderUser = TableRegistry::getTableLocator()->get('Users')->find()
                ->select(['id', 'display_name', 'username'])
                ->where(['id' => (int)$bpProcurement->sender])
                ->first();
        }
        $salesStatusLabels = BP_PROCUREMENT_STATUS_LABELS;
        $salesReasonLabels = BP_PROCUREMENT_REASON_LABELS;

        $this->set(compact('bpProcurement', 'matchedContact', 'salesStatusLabels', 'salesReasonLabels', 'senderUser'));
    }

    public function updateSalesResult()
    {
        $this->request->allowMethod(['post']);

        $id = (int)$this->request->getData('procurement_id');
        if ($id <= 0) {
            $this->Flash->error(__('Invalid bp procurement.'));

            return $this->redirect(['action' => 'index']);
        }

        $bpProcurement = $this->BpProcurements->get($id);
        $data = [];
        if ($this->request->getData('sales_status') !== null) {
            $data['sales_status'] = $this->normalizeOptionValue(
                $this->request->getData('sales_status'),
                BP_PROCUREMENT_STATUS_LABELS
            );
        }
        if ($this->request->getData('sales_reason') !== null) {
            $data['sales_reason'] = $this->normalizeOptionValue(
                $this->request->getData('sales_reason'),
                BP_PROCUREMENT_REASON_LABELS
            );
        }
        if ($this->request->getData('evaluation') !== null) {
            $data['evaluation'] = $this->normalizeOptionValue(
                $this->request->getData('evaluation'),
                BP_PROCUREMENT_EVALUATION_LABELS
            );
        }

        if ($data === []) {
            $this->Flash->error(__('No update data was provided.'));

            return $this->redirect(['action' => 'index']);
        }

        $bpProcurement = $this->BpProcurements->patchEntity($bpProcurement, $data);
        if ($this->BpProcurements->save($bpProcurement)) {
            $this->Flash->success(__('The bp procurement has been saved.'));
        } else {
            $this->Flash->error(__('The bp procurement could not be saved. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function syncMailbox(): void
    {
        $service = new Pop3BpProcurementService($this->BpProcurements, (array)Configure::read('Pop3BpProcurement', []));

        try {
            $count = $service->import();
            $this->Flash->success(__('Mail sync completed. {0} new message(s) imported.', $count));
        } catch (\Throwable $e) {
            Log::error('BP procurement POP3 sync failed: ' . $e->getMessage());
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

        $bpContactsTable = TableRegistry::getTableLocator()->get('BpContacts');

        return $bpContactsTable->find()
            ->select(['id', 'bp_id', 'name', 'email'])
            ->where(['BpContacts.email IN' => $emails])
            ->contain(['Bps' => function ($q) {
                return $q->select(['id', 'name']);
            }])
            ->first();
    }

    /**
     * @param iterable<\App\Model\Entity\BpProcurement> $bpProcurements
     * @return array<int, \Cake\Datasource\EntityInterface>
     */
    private function buildSenderUserMap(iterable $bpProcurements): array
    {
        $senderUserIds = [];
        foreach ($bpProcurements as $procurement) {
            $sender = (string)$procurement->sender;
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
     * @return array<int, array{name:string,count:int,sender:string}>
     */
    private function buildSalesCountBadges(FrozenTime $from, FrozenTime $to): array
    {
        $query = $this->BpProcurements->find();
        $rows = $query
            ->select([
                'sender',
                'procurement_count' => $query->func()->count('id'),
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
            $count = (int)($row['procurement_count'] ?? 0);
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
