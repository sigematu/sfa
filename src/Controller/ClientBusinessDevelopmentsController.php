<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Throwable;

/**
 * ClientBusinessDevelopments Controller
 *
 * @property \App\Model\Table\ClientBusinessDevelopmentsTable $ClientBusinessDevelopments
 */
class ClientBusinessDevelopmentsController extends AppController
{
    public $paginate = [
        'order' => ['ClientBusinessDevelopments.action_at' => 'DESC', 'ClientBusinessDevelopments.id' => 'DESC'],
        'limit' => 50,
    ];

    public function index()
    {
        $query = $this->ClientBusinessDevelopments->find()
            ->contain(['Users', 'Clients', 'ClientContacts']);

        $searchStatus = $this->normalizeOptionValue($this->request->getQuery('sales_status'), CLIENT_BIZ_DEV_SALES_STATUS_LABELS);
        $searchReason = $this->normalizeOptionValue($this->request->getQuery('sales_reason'), CLIENT_BIZ_DEV_REASON_LABELS);
        $searchUserId = (int)$this->request->getQuery('user_id');
        $dateFrom = trim((string)$this->request->getQuery('date_from'));
        $dateTo = trim((string)$this->request->getQuery('date_to'));

        if ($dateFrom !== '' && $dateTo !== '') {
            try {
                $query->where([
                    'ClientBusinessDevelopments.action_at >=' => new FrozenTime($dateFrom . ' 00:00:00'),
                    'ClientBusinessDevelopments.action_at <=' => new FrozenTime($dateTo . ' 23:59:59'),
                ]);
            } catch (Throwable $e) {
                // invalid date — ignore
            }
        }

        if ($searchStatus !== null) {
            $query->where(['ClientBusinessDevelopments.sales_status' => $searchStatus]);
        }
        if ($searchReason !== null) {
            $query->where(['ClientBusinessDevelopments.sales_reason' => $searchReason]);
        }
        if ($searchUserId > 0) {
            $query->where(['ClientBusinessDevelopments.user_id' => $searchUserId]);
        }

        $records = $this->paginate($query);

        $salesStatusLabels = CLIENT_BIZ_DEV_SALES_STATUS_LABELS;
        $salesReasonLabels = CLIENT_BIZ_DEV_REASON_LABELS;
        $userOptions = $this->buildUserOptions();

        $this->set(compact(
            'records',
            'salesStatusLabels',
            'salesReasonLabels',
            'userOptions',
            'searchStatus',
            'searchReason',
            'searchUserId'
        ));
    }

    public function add()
    {
        $record = $this->ClientBusinessDevelopments->newEmptyEntity();

        if (!$this->request->is('post')) {
            $clientId = (int)$this->request->getQuery('client_id');
            $clientContactId = (int)$this->request->getQuery('client_contact_id');
            if ($clientId > 0) {
                $record->client_id = $clientId;
            }
            if ($clientContactId > 0) {
                $record->client_contact_id = $clientContactId;
            }
        }

        if ($this->request->is('post')) {
            $identity = $this->Authentication->getIdentity();
            $userId = (int)($identity ? $identity->getOriginalData()['id'] : 0);

            $data = (array)$this->request->getData();
            $data['user_id'] = $userId;

            $record = $this->ClientBusinessDevelopments->patchEntity($record, $data);
            if ($this->ClientBusinessDevelopments->save($record)) {
                $this->Flash->success(__('保存しました。'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('保存できませんでした。入力内容を確認してください。'));
        }

        $salesStatusLabels = CLIENT_BIZ_DEV_SALES_STATUS_LABELS;
        $salesReasonLabels = CLIENT_BIZ_DEV_REASON_LABELS;
        $clientOptions = $this->buildClientOptions();
        $clientContactsData = $this->buildClientContactsData();

        $this->set(compact(
            'record',
            'salesStatusLabels',
            'salesReasonLabels',
            'clientOptions',
            'clientContactsData'
        ));
    }

    public function edit($id = null)
    {
        $record = $this->ClientBusinessDevelopments->get((int)$id);

        if ($this->request->is(['post', 'put'])) {
            $data = (array)$this->request->getData();
            $record = $this->ClientBusinessDevelopments->patchEntity($record, $data);
            if ($this->ClientBusinessDevelopments->save($record)) {
                $this->Flash->success(__('保存しました。'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('保存できませんでした。入力内容を確認してください。'));
        }

        $salesStatusLabels = CLIENT_BIZ_DEV_SALES_STATUS_LABELS;
        $salesReasonLabels = CLIENT_BIZ_DEV_REASON_LABELS;
        $clientOptions = $this->buildClientOptions();
        $clientContactsData = $this->buildClientContactsData();

        $this->set(compact(
            'record',
            'salesStatusLabels',
            'salesReasonLabels',
            'clientOptions',
            'clientContactsData'
        ));

        $this->render('add');
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
    private function buildUserOptions(): array
    {
        $users = TableRegistry::getTableLocator()->get('Users')->find()
            ->select(['id', 'display_name', 'username'])
            ->orderAsc('display_name')
            ->all();

        $options = [];
        foreach ($users as $user) {
            $name = (string)($user->display_name ?? '');
            if ($name === '') {
                $name = (string)($user->username ?? '');
            }
            $options[(int)$user->id] = $name;
        }

        return $options;
    }

    /**
     * @return array<int, string>
     */
    private function buildClientOptions(): array
    {
        $clients = TableRegistry::getTableLocator()->get('Clients')->find()
            ->select(['id', 'name'])
            ->orderAsc('name')
            ->all();

        $options = [];
        foreach ($clients as $client) {
            $options[(int)$client->id] = (string)$client->name;
        }

        return $options;
    }

    /**
     * @return array<int, array{id:int,client_id:int,name:string}>
     */
    private function buildClientContactsData(): array
    {
        $contacts = TableRegistry::getTableLocator()->get('ClientContacts')->find()
            ->select(['id', 'client_id', 'name'])
            ->orderAsc('name')
            ->all();

        $data = [];
        foreach ($contacts as $contact) {
            $data[] = [
                'id' => (int)$contact->id,
                'client_id' => (int)$contact->client_id,
                'name' => (string)$contact->name,
            ];
        }

        return $data;
    }
}
