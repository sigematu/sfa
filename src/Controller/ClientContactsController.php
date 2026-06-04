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
        $clientContacts = $this->ClientContacts
            ->find('search', ['search' => $this->request->getQueryParams()])
            ->contain(['Clients']);

        $this->set(compact('clientContacts'), $this->paginate($clientContacts));

        $clients = $this->ClientContacts->Clients->find('list', ['order' => ['kana' => 'ASC']])->all();
        $this->set(compact('clients'));
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

        $this->set(compact('clientContact'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $clientContact = $this->ClientContacts->newEmptyEntity();
        if ($this->request->is('post')) {
            $clientContact = $this->ClientContacts->patchEntity($clientContact, $this->request->getData());
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
        $this->set(compact('clientContact', 'clients', 'client_id'));
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
        if ($this->request->is(['patch', 'post', 'put'])) {
            $clientContact = $this->ClientContacts->patchEntity($clientContact, $this->request->getData());
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
        $this->set(compact('clientContact', 'clients'));
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
