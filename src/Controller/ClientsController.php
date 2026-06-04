<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Mailer\Mailer;

/**
 * Clients Controller
 *
 * @property \App\Model\Table\ClientsTable $Clients
 * @method \App\Model\Entity\Client[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ClientsController extends AppController
{
    public $paginate = [
        'order' => ['Clients.id' => 'DESC'],
    ];
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Search.Search', [
            'actions' => ['index'],
        ]);
        $this->loadComponent('Pic');
        $this->loadComponent('MailNotify');
        $this->loadComponent('GetName');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $clients = $this->Clients
            ->find('search', ['search' => $this->request->getQueryParams()])
            ->contain(['Users']);

        $this->set(compact('clients'), $this->paginate($clients));
    }

    /**
     * View method
     *
     * @param string|null $id Client id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sq_created_dn = $this->Pic->getDisplayName($this->name, 'created_id');
        $sq_modified_dn = $this->Pic->getDisplayName($this->name, 'modified_id');

        $client = $this->Clients
            ->get($id, [
                'fields' => [
                    'Clients.id',
                    'Clients.name',
                    'Clients.kana',
                    'Clients.url',
                    'Clients.sales_rank',
                    'Clients.note',
                    'Clients.status',
                    'Clients.created',
                    'Clients.created_id',
                    'Clients.modified',
                    'Clients.modified_id',
                    'created_dn' => $sq_created_dn,
                    'modified_dn' => $sq_modified_dn
                ],
                'contain' => [
                    'Users',
                    'ClientContacts' => function ($q) {
                        return $q
                            ->where(['ClientContacts.status' => STATUS_ACTIVE])
                            ->order(['ClientContacts.id' => 'DESC'])
                            ->limit(20);
                    },
                ],
            ]);

        $this->set(compact('client'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $client = $this->Clients->newEmptyEntity();
        if ($this->request->is('post')) {
            $client = $this->Clients->patchEntity($client, $this->request->getData());
            if ($savedData = $this->Clients->save($client)) {
                $this->Flash->success(__('The client has been saved.'));

                // メール送信
                $controller = $this->request->getParam('controller');
                $action = $this->request->getParam('action');
                $this->MailNotify->mailSend($savedData, $controller, $action);

                return $this->redirect(['action' => 'view', $savedData->id]);
            }
            $this->Flash->error(__('The client could not be saved. Please, try again.'));
        }
        $users = $this->Clients->Users->find('list')->all();
        $this->set(compact('client', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Client id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $client = $this->Clients->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $client = $this->Clients->patchEntity($client, $this->request->getData());
            if ($savedData = $this->Clients->save($client)) {
                $this->Flash->success(__('The client has been saved.'));

                // メール送信
                $controller = $this->request->getParam('controller');
                $action = $this->request->getParam('action');
                $this->MailNotify->mailSend($savedData, $controller, $action);

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The client could not be saved. Please, try again.'));
        }
        $users = $this->Clients->Users->find('list')->all();
        $this->set(compact('client', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Client id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $client = $this->Clients->get($id);

        // メール送信用に削除対象の名前を保存
        $savedData = (object)[
            'name' => $client->name
        ];
        if ($this->Clients->delete($client)) {
            $this->Flash->success(__('The client has been deleted.'));

            // メール送信
            $controller = $this->request->getParam('controller');
            $action = $this->request->getParam('action');
            $this->MailNotify->mailSend($savedData, $controller, $action);
        } else {
            $this->Flash->error(__('The client could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
