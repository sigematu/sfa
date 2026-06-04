<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * BpContacts Controller
 *
 * @property \App\Model\Table\BpContactsTable $BpContacts
 * @method \App\Model\Entity\BpContact[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BpContactsController extends AppController
{
    public $paginate = [
        'order' => ['BpContacts.id' => 'DESC'],
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
        $bpContacts = $this->BpContacts
            ->find('search', ['search' => $this->request->getQueryParams()])
            ->contain(['Bps']);

        $this->set(compact('bpContacts'), $this->paginate($bpContacts));

        $bps = $this->BpContacts->Bps->find('list', ['order' => ['kana' => 'ASC']])->all();
        $this->set(compact('bps'));
    }

    /**
     * View method
     *
     * @param string|null $id Bp Contact id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $bpContact = $this->BpContacts->get($id, [
            'contain' => ['Bps'],
        ]);

        $this->set(compact('bpContact'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $bpContact = $this->BpContacts->newEmptyEntity();
        if ($this->request->is('post')) {
            $bpContact = $this->BpContacts->patchEntity($bpContact, $this->request->getData());
            if ($savedData = $this->BpContacts->save($bpContact)) {
                $this->Flash->success(__('The bp contact has been saved.'));

                // メール送信
                $controller = $this->request->getParam('controller');
                $action = $this->request->getParam('action');
                $this->MailNotify->mailSend($savedData, $controller, $action);

                return $this->redirect(['action' => 'view', $savedData->id]);
            }
            $this->Flash->error(__('The bp contact could not be saved. Please, try again.'));
        }

        // URLパラメータから bp_id を取得して初期値にセット
        $bp_id = $this->request->getQuery('bp_id');
        $bps = $this->BpContacts->Bps->find('list', ['order' => ['kana' => 'ASC']])->all();
        $this->set(compact('bpContact', 'bps', 'bp_id'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Bp Contact id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $bpContact = $this->BpContacts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $bpContact = $this->BpContacts->patchEntity($bpContact, $this->request->getData());
            if ($savedData = $this->BpContacts->save($bpContact)) {
                $this->Flash->success(__('The bp contact has been saved.'));

                // メール送信
                $controller = $this->request->getParam('controller');
                $action = $this->request->getParam('action');
                $this->MailNotify->mailSend($savedData, $controller, $action);

                return $this->redirect(['action' => 'view', $savedData->id]);
            }
            $this->Flash->error(__('The bp contact could not be saved. Please, try again.'));
        }
        $bps = $this->BpContacts->Bps->find('list', ['order' => ['kana' => 'ASC']])->all();
        $this->set(compact('bpContact', 'bps'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Bp Contact id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $bpContact = $this->BpContacts->get($id);

        // メール送信用に削除対象の名前を保存
        $savedData = (object)[
            'name' => $bpContact->name
        ];

        if ($this->BpContacts->delete($bpContact)) {
            $this->Flash->success(__('The bp contact has been deleted.'));

            // メール送信
            $controller = $this->request->getParam('controller');
            $action = $this->request->getParam('action');
            $this->MailNotify->mailSend($savedData, $controller, $action);
        } else {
            $this->Flash->error(__('The bp contact could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
