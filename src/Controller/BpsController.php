<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\UsersTable;
use App\Service\BpImportService;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Bps Controller
 *
 * @property \App\Model\Table\BpsTable $Bps
 * @method \App\Model\Entity\Bp[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BpsController extends AppController
{
    public $paginate = [
        'order' => ['Bps.id' => 'DESC'],
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
        $bps = $this->Bps
            ->find('search', ['search' => $this->request->getQueryParams()])
            ->contain(['Users']);

        $this->set(compact('bps'), $this->paginate($bps));
    }

    /**
     * View method
     *
     * @param string|null $id Bp id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sq_created_dn = $this->Pic->getDisplayName($this->name, 'created_id');
        $sq_modified_dn = $this->Pic->getDisplayName($this->name, 'modified_id');

        try {
            $bp = $this->Bps
                ->get($id, [
                    'fields' => [
                        'Bps.id',
                        'Bps.name',
                        'Bps.kana',
                        'Bps.url',
                        'Bps.invoice_number',
                        'Bps.location',
                        'Bps.categories',
                        'Bps.note',
                        'Bps.status',
                        'Bps.created',
                        'Bps.created_id',
                        'Bps.modified',
                        'Bps.modified_id',
                        'created_dn' => $sq_created_dn,
                        'modified_dn' => $sq_modified_dn
                    ],
                    'contain' => [
                        'Users',
                        'BpContacts' => function ($q) {
                            return $q
                                ->where(['BpContacts.status' => STATUS_ACTIVE])
                                ->order(['BpContacts.id' => 'DESC'])
                                ->limit(20);
                        },
                    ],
                ]);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('The bp was not found.'));

            return $this->redirect(['action' => 'index']);
        }

        $categoryLabels = [];
        if (!empty($bp->categories)) {
            foreach (explode(',', (string)$bp->categories) as $category) {
                $key = (int)$category;
                if (isset(BP_CATEGORY_LABELS[$key])) {
                    $categoryLabels[] = BP_CATEGORY_LABELS[$key];
                }
            }
        }

        $this->set(compact('bp', 'categoryLabels'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $bp = $this->Bps->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $selectedCategories = array_values(array_unique(array_filter((array)($data['categories'] ?? []), function ($value) {
                return isset(BP_CATEGORY_LABELS[(int)$value]);
            })));
            sort($selectedCategories);
            $data['categories'] = implode(',', $selectedCategories);

            $bp = $this->Bps->patchEntity($bp, $data);
            if ($savedData = $this->Bps->save($bp)) {
                $this->Flash->success(__('The bp has been saved.'));

                // メール送信
                $controller = $this->request->getParam('controller');
                $action = $this->request->getParam('action');
                $this->MailNotify->mailSend($savedData, $controller, $action);

                return $this->redirect(['action' => 'view', $savedData->id]);
            }
            $this->Flash->error(__('The bp could not be saved. Please, try again.'));
        }
        $users = $this->Bps->Users->find('list')->all();
        $locations = BP_LOCATION_LABELS;
        $bpCategories = BP_CATEGORY_LABELS;
        $this->set(compact('bp', 'users', 'locations', 'bpCategories'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Bp id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        try {
            $bp = $this->Bps->get($id, [
                'contain' => [],
            ]);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('The bp was not found.'));

            return $this->redirect(['action' => 'index']);
        }

        if (!empty($bp->categories)) {
            $bp->categories = explode(',', (string)$bp->categories);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $selectedCategories = array_values(array_unique(array_filter((array)($data['categories'] ?? []), function ($value) {
                return isset(BP_CATEGORY_LABELS[(int)$value]);
            })));
            sort($selectedCategories);
            $data['categories'] = implode(',', $selectedCategories);

            $bp = $this->Bps->patchEntity($bp, $data);
            if ($savedData = $this->Bps->save($bp)) {
                $this->Flash->success(__('The bp has been saved.'));

                // メール送信
                $controller = $this->request->getParam('controller');
                $action = $this->request->getParam('action');
                $this->MailNotify->mailSend($savedData, $controller, $action);

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The bp could not be saved. Please, try again.'));
        }
        $users = $this->Bps->Users->find('list')->all();
        $locations = BP_LOCATION_LABELS;
        $bpCategories = BP_CATEGORY_LABELS;
        $this->set(compact('bp', 'users', 'locations', 'bpCategories'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Bp id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $bp = $this->Bps->get($id);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('The bp was not found.'));

            return $this->redirect(['action' => 'index']);
        }

        // メール送信用に削除対象の名前を保存
        $savedData = (object)[
            'name' => $bp->name
        ];
        if ($this->Bps->delete($bp)) {
            $this->Flash->success(__('The bp has been deleted.'));

            // メール送信
            $controller = $this->request->getParam('controller');
            $action = $this->request->getParam('action');
            $this->MailNotify->mailSend($savedData, $controller, $action);
        } else {
            $this->Flash->error(__('The bp could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Import method (CSV). Admin only.
     *
     * @return \Cake\Http\Response|null
     */
    public function import()
    {
        $this->request->allowMethod(['post']);

        $role = (string)$this->request->getSession()->read('Auth.role');
        if ($role !== UsersTable::ROLE_ADMIN) {
            $this->Flash->error(__('You are not authorized to import bps.'));

            return $this->redirect(['action' => 'index']);
        }

        $upload = $this->request->getData('import_file');
        if (!$upload instanceof \Psr\Http\Message\UploadedFileInterface) {
            $this->Flash->error(__('Import file was not provided.'));

            return $this->redirect(['action' => 'index']);
        }

        if ($upload->getError() !== UPLOAD_ERR_OK) {
            $this->Flash->error(__('Failed to upload import file.'));

            return $this->redirect(['action' => 'index']);
        }

        $filename = (string)$upload->getClientFilename();
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'csv') {
            $this->Flash->error(__('Only CSV files are supported.'));

            return $this->redirect(['action' => 'index']);
        }

        $tmpPath = TMP . 'bp_import_' . uniqid('', true) . '.csv';
        try {
            $upload->moveTo($tmpPath);
        } catch (\Throwable $e) {
            $this->Flash->error(__('Failed to upload import file.'));

            return $this->redirect(['action' => 'index']);
        }

        $operatorId = (string)$this->request->getSession()->read('Auth.id');

        try {
            $stats = (new BpImportService())->import($tmpPath, $operatorId);

            $message = __(
                'BP import completed. BP(created: {0}, updated: {1}), contact(created: {2}, updated: {3}), skipped: {4}.',
                $stats['bp_created'],
                $stats['bp_updated'],
                $stats['contact_created'],
                $stats['contact_updated'],
                $stats['bp_skipped']
            );
            if (!empty($stats['warnings'])) {
                $message .= ' ' . __('warnings: {0}', count($stats['warnings']));
            }
            $this->Flash->success($message);
        } catch (\Throwable $e) {
            $this->Flash->error(__('Import failed. {0}', $e->getMessage()));
        } finally {
            if (is_file($tmpPath)) {
                @unlink($tmpPath);
            }
        }

        return $this->redirect(['action' => 'index']);
    }
}
