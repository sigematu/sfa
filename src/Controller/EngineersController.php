<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Engineers Controller
 *
 * @property \App\Model\Table\EngineersTable $Engineers
 * @method \App\Model\Entity\Engineer[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EngineersController extends AppController
{
    public $paginate = [
        'order' => ['Engineers.id' => 'DESC'],
    ];
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Search.Search', [
            'actions' => ['index'],
        ]);
        $this->loadComponent('Pic');
        $this->loadComponent('MailNotify');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */

    public function index()
    {
        $engineers = $this->Engineers
            ->find('search', ['search' => $this->request->getQueryParams()])
            ;

        $this->set(compact('engineers'), $this->paginate($engineers));
    }

    /**
     * View method
     *
     * @param string|null $id Engineer id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sq_created_dn = $this->Pic->getDisplayName($this->name, 'created_id');
        $sq_modified_dn = $this->Pic->getDisplayName($this->name, 'modified_id');

        $engineer = $this->Engineers
            ->get($id, [
                'fields' => [
                    'Engineers.id',
                    'Engineers.emp_no',
                    'Engineers.name',
                    'Engineers.kana',
                    'Engineers.birthyear',
                    'Engineers.year_industory_exp',
                    'Engineers.skill_exp',
                    'Engineers.year_skill_exp',
                    'Engineers.skill_sheet',
                    'Engineers.note',
                    'Engineers.status',
                    'Engineers.created',
                    'Engineers.created_id',
                    'Engineers.modified',
                    'Engineers.modified_id',
                    'created_dn' => $sq_created_dn,
                    'modified_dn' => $sq_modified_dn
                ],
                'contain' => ['Users']
            ]);

        $this->set(compact('engineer'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $engineer = $this->Engineers->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            // Remove the file object from data before patching to avoid conversion errors
            if (isset($data['skill_sheet']) && $data['skill_sheet'] instanceof \Psr\Http\Message\UploadedFileInterface) {
                unset($data['skill_sheet']);
            }
            $attachment = $this->request->getData('skill_sheet');

            if ($attachment && $attachment->getError() === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($attachment->getClientFilename(), PATHINFO_EXTENSION));
                if (!in_array($extension, ['xlsx', 'xls', 'docx', 'doc', 'pdf'])) {
                    $this->Flash->error(__('Only Excel, Word and PDF files are allowed.'));
                    return $this->redirect(['action' => 'add']);
                }
            }

            $engineer = $this->Engineers->patchEntity($engineer, $data);
            if ($savedData = $this->Engineers->save($engineer)) {
                if ($attachment && $attachment->getError() === UPLOAD_ERR_OK) {
                    $extension = pathinfo($attachment->getClientFilename(), PATHINFO_EXTENSION);
                    $name = $savedData->id . '_' . $savedData->name . '.' . $extension;
                    $targetPath = ROOT . DS . 'storage' . DS . 'engineers' . DS . $name;
                    
                    $dir = dirname($targetPath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0775, true);
                    }

                    $attachment->moveTo($targetPath);
                    $savedData->skill_sheet = $name;
                    $this->Engineers->save($savedData);
                }
                
                $this->Flash->success(__('The engineer has been saved.'));

                // メール送信
                $controller = $this->request->getParam('controller');
                $action = $this->request->getParam('action');
                $this->MailNotify->mailSend($savedData, $controller, $action);

                return $this->redirect(['action' => 'view', $savedData->id]);
            }
            $this->Flash->error(__('The engineer could not be saved. Please, try again.'));
        }
        $this->set(compact('engineer'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Engineer id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $engineer = $this->Engineers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            // Remove the file object from data before patching to avoid conversion errors
            if (isset($data['skill_sheet']) && $data['skill_sheet'] instanceof \Psr\Http\Message\UploadedFileInterface) {
                unset($data['skill_sheet']);
            }
            $attachment = $this->request->getData('skill_sheet');

            if ($attachment && $attachment instanceof \Psr\Http\Message\UploadedFileInterface && $attachment->getError() === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($attachment->getClientFilename(), PATHINFO_EXTENSION));
                if (!in_array($extension, ['xlsx', 'xls', 'docx', 'doc', 'pdf'])) {
                    $this->Flash->error(__('Only Excel, Word and PDF files are allowed.'));
                    return $this->redirect(['action' => 'edit', $id]);
                }
            }

            $engineer = $this->Engineers->patchEntity($engineer, $data);
            if ($savedData = $this->Engineers->save($engineer)) {
                if ($attachment && $attachment instanceof \Psr\Http\Message\UploadedFileInterface && $attachment->getError() === UPLOAD_ERR_OK) {
                    $extension = pathinfo($attachment->getClientFilename(), PATHINFO_EXTENSION);
                    $name = $savedData->id . '_' . $savedData->name . '.' . $extension;
                    $targetPath = ROOT . DS . 'storage' . DS . 'engineers' . DS . $name;
                    
                    $dir = dirname($targetPath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0775, true);
                    }

                    $attachment->moveTo($targetPath);
                    $savedData->skill_sheet = $name;
                    $this->Engineers->save($savedData);
                }
                
                $this->Flash->success(__('The engineer has been saved.'));

                // メール送信
                $controller = $this->request->getParam('controller');
                $action = $this->request->getParam('action');
                $this->MailNotify->mailSend($savedData, $controller, $action);

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The engineer could not be saved. Please, try again.'));
        }
        $this->set(compact('engineer'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Engineer id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $engineer = $this->Engineers->get($id);

        // メール送信用に削除対象の名前を保存
        $savedData = (object)[
            'name' => $engineer->name
        ];

        $filePath = WWW_ROOT . 'files' . DS . 'engineers' . DS . $engineer->skill_sheet;
        if (!empty($engineer->skill_sheet) && file_exists($filePath)) {
            unlink($filePath);
        }

        if ($this->Engineers->delete($engineer)) {
            $this->Flash->success(__('The engineer has been deleted.'));

            // メール送信
            $controller = $this->request->getParam('controller');
            $action = $this->request->getParam('action');
            $this->MailNotify->mailSend($savedData, $controller, $action);
        } else {
            $this->Flash->error(__('The engineer could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Download method
     * * @param string|null $id Engineer id.
     * @return \Cake\Http\Response|null
     */
    public function download($id = null)
    {
        $engineer = $this->Engineers->get($id);
        
        if (empty($engineer->skill_sheet)) {
            $this->Flash->error(__('File not found.'));
            return $this->redirect(['action' => 'view', $id]);
        }

        $filePath = ROOT . DS . 'storage' . DS . 'engineers' . DS . $engineer->skill_sheet;

        if (!file_exists($filePath)) {
            $this->Flash->error(__('File does not exist on server.'));
            return $this->redirect(['action' => 'view', $id]);
        }

        $response = $this->response->withFile($filePath, [
            'download' => true,
            'name' => $engineer->skill_sheet,
        ]);

        return $response;
    }
}
