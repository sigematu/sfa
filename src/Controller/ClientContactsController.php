<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * ClientContacts Controller
 *
 * @property \App\Model\Table\ClientContactsTable $ClientContacts
 * @method \App\Model\Entity\ClientContact[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ClientContactsController extends AppController
{
    public $paginate = [
        'order' => ['ClientContacts.id' => 'DESC'],
    ];
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Search.Search', [
            'actions' => ['index'],
        ]);
        $this->loadComponent('MailNotify');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $searchParams = $this->request->getQueryParams();
        if (!array_key_exists('status', $searchParams) || $searchParams['status'] === '') {
            $searchParams['status'] = (string)STATUS_ACTIVE;
            $this->request = $this->request->withQueryParams($searchParams);
        }

        $query = $this->ClientContacts
            ->find('search', ['search' => $searchParams])
            ->contain(['Clients']);

        $clientContacts = $this->paginate($query);

        $hierarchyIds = [];
        foreach ($clientContacts as $clientContact) {
            $hierarchy = (int)($clientContact->hierarchy ?? 0);
            if ($hierarchy > 0) {
                $hierarchyIds[$hierarchy] = $hierarchy;
            }
        }

        $hierarchyMap = [];
        if (!empty($hierarchyIds)) {
            $hierarchyMap = $this->ClientContacts->find('list', [
                'keyField' => 'id',
                'valueField' => 'name',
            ])
                ->where(['id IN' => array_values($hierarchyIds)])
                ->toArray();
        }

        $clients = $this->ClientContacts->Clients->find('list', ['order' => ['kana' => 'ASC']])->all();
        $this->set(compact('clientContacts', 'clients', 'hierarchyMap'));
    }

    /**
     * View method
     *
     * @param string|null $id Client Contact id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $clientContact = $this->ClientContacts->get($id, [
            'contain' => ['Clients'],
        ]);

        $hierarchyName = '';
        if (!empty($clientContact->hierarchy)) {
            $parent = $this->ClientContacts->find()
                ->select(['id', 'name', 'client_id'])
                ->where([
                    'id' => $clientContact->hierarchy,
                    'client_id' => $clientContact->client_id,
                ])
                ->first();

            if ($parent) {
                $hierarchyName = $parent->name;
            }
        }

        $this->set(compact('clientContact', 'hierarchyName'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $clientContact = $this->ClientContacts->newEmptyEntity();
        $hierarchyOptionsByClient = $this->getHierarchyOptionsByClient();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data = $this->normalizeClientContactOptionalFields($data, $hierarchyOptionsByClient, null);
            $clientContact = $this->ClientContacts->patchEntity($clientContact, $data);
            if ($savedData = $this->ClientContacts->save($clientContact)) {
                $this->Flash->success(__('The client contact has been saved.'));

                // メール送信
                $controller = $this->request->getParam('controller');
                $action = $this->request->getParam('action');
                $this->MailNotify->mailSend($savedData, $controller, $action);

                return $this->redirect(['action' => 'view', $savedData->id]);
            }
            $this->Flash->error(__('The client contact could not be saved. Please, try again.'));
        }

        // URLパラメータから client_id を取得して初期値にセット
        $client_id = $this->request->getQuery('client_id');
        $clients = $this->ClientContacts->Clients->find('list', ['order' => ['kana' => 'ASC']])->all();
        $categories = CLIENT_CONTACT_CATEGORY_LABELS;
        $inactiveReasons = CLIENT_CONTACT_INACTIVE_REASON_LABELS;
        $this->set(compact('clientContact', 'clients', 'client_id', 'categories', 'inactiveReasons', 'hierarchyOptionsByClient'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Client Contact id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $clientContact = $this->ClientContacts->get($id, [
            'contain' => [],
        ]);

        $hierarchyOptionsByClient = $this->getHierarchyOptionsByClient((int)$id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $data = $this->normalizeClientContactOptionalFields($data, $hierarchyOptionsByClient, (int)$id);
            $clientContact = $this->ClientContacts->patchEntity($clientContact, $data);
            if ($savedData = $this->ClientContacts->save($clientContact)) {
                $this->Flash->success(__('The client contact has been saved.'));

                // メール送信
                $controller = $this->request->getParam('controller');
                $action = $this->request->getParam('action');
                $this->MailNotify->mailSend($savedData, $controller, $action);

                return $this->redirect(['action' => 'view', $savedData->id]);
            }
            $this->Flash->error(__('The client contact could not be saved. Please, try again.'));
        }
        $clients = $this->ClientContacts->Clients->find('list', ['order' => ['kana' => 'ASC']])->all();
        $categories = CLIENT_CONTACT_CATEGORY_LABELS;
        $inactiveReasons = CLIENT_CONTACT_INACTIVE_REASON_LABELS;
        $this->set(compact('clientContact', 'clients', 'categories', 'inactiveReasons', 'hierarchyOptionsByClient'));
    }

    /**
     * @param int|null $excludeId Exclude this contact id from hierarchy options.
     * @return array<int, array<int, string>>
     */
    private function getHierarchyOptionsByClient(?int $excludeId = null): array
    {
        $query = $this->ClientContacts->find()
            ->select(['id', 'client_id', 'name'])
            ->order(['client_id' => 'ASC', 'name' => 'ASC']);

        if ($excludeId !== null) {
            $query->where(['id !=' => $excludeId]);
        }

        $options = [];
        foreach ($query->all() as $contact) {
            $clientId = (int)$contact->client_id;
            $options[$clientId][(int)$contact->id] = $contact->name;
        }

        return $options;
    }

    /**
     * @param array $data Request data
     * @param array<int, array<int, string>> $hierarchyOptionsByClient
     * @param int|null $selfId Current contact id (edit only)
     * @return array
     */
    private function normalizeClientContactOptionalFields(array $data, array $hierarchyOptionsByClient, ?int $selfId): array
    {
        $department = trim((string)($data['department'] ?? ''));
        $data['department'] = $department === '' ? null : $department;

        $data['category'] = isset(CLIENT_CONTACT_CATEGORY_LABELS[(int)($data['category'] ?? 0)])
            ? (int)$data['category']
            : null;

        $clientId = (int)($data['client_id'] ?? 0);
        $hierarchy = (int)($data['hierarchy'] ?? 0);
        $isSelfSelected = $selfId !== null && $hierarchy === $selfId;

        if (
            $hierarchy === 0 ||
            $isSelfSelected ||
            !isset($hierarchyOptionsByClient[$clientId][$hierarchy])
        ) {
            $data['hierarchy'] = null;
        } else {
            $data['hierarchy'] = $hierarchy;
        }

        $status = (int)($data['status'] ?? 0);
        $inactiveReason = (int)($data['inactive_reason'] ?? 0);
        if ($status === STATUS_INACTIVE && isset(CLIENT_CONTACT_INACTIVE_REASON_LABELS[$inactiveReason])) {
            $data['inactive_reason'] = $inactiveReason;
        } else {
            $data['inactive_reason'] = null;
        }

        return $data;
    }

    /**
     * Delete method
     *
     * @param string|null $id Client Contact id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $clientContact = $this->ClientContacts->get($id);

        // メール送信用に削除対象の名前を保存
        $savedData = (object)[
            'name' => $clientContact->name
        ];

        if ($this->ClientContacts->delete($clientContact)) {
            $this->Flash->success(__('The client contact has been deleted.'));

            // メール送信
            $controller = $this->request->getParam('controller');
            $action = $this->request->getParam('action');
            $this->MailNotify->mailSend($savedData, $controller, $action);
        } else {
            $this->Flash->error(__('The client contact could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
