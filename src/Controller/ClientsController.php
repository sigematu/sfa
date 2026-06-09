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
                    'Clients.group_name',
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
        $this->set(compact('client'));
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
        $this->set(compact('client'));
    }

    /**
     * Import method (CSV)
     *
     * @return \Cake\Http\Response|null
     */
    public function import()
    {
        $this->request->allowMethod(['post']);

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
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            $this->Flash->error(__('Only CSV files are supported.'));

            return $this->redirect(['action' => 'index']);
        }

        $stream = $upload->getStream();
        $csv = (string)$stream;
        if ($csv === '') {
            $this->Flash->error(__('Import file is empty.'));

            return $this->redirect(['action' => 'index']);
        }

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csv);
        rewind($handle);

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            $this->Flash->error(__('Header row could not be read.'));

            return $this->redirect(['action' => 'index']);
        }

        $headerMap = $this->buildImportHeaderMap($header);
        $colName = $this->findImportColumn($headerMap, ['name', 'company', '会社名', '顧客名']);
        if ($colName === null) {
            fclose($handle);
            $this->Flash->error(__('Required header is missing: name'));

            return $this->redirect(['action' => 'index']);
        }

        $colKana = $this->findImportColumn($headerMap, ['kana', '会社カナ', 'カナ']);
        $colUrl = $this->findImportColumn($headerMap, ['url', 'URL']);
        $colSalesRank = $this->findImportColumn($headerMap, ['sales_rank', '売上ランク']);
        $colGroupName = $this->findImportColumn($headerMap, ['group_name', 'グループ']);
        $colNote = $this->findImportColumn($headerMap, ['note', '備考']);
        $colStatus = $this->findImportColumn($headerMap, ['status', 'ステータス']);

        $authId = (string)$this->request->getSession()->read('Auth.id');
        $activeStatus = defined('STATUS_ACTIVE') ? (int)STATUS_ACTIVE : 1;

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $urlCleared = 0;
        $updatedNames = [];
        $skippedNames = [];
        $urlClearedNames = [];

        $conn = $this->Clients->getConnection();
        $conn->begin();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $name = $this->importCell($row, $colName);
                if ($name === '') {
                    $skipped++;
                    $skippedNames[] = __('(empty row)');
                    continue;
                }

                $entity = $this->Clients->find()->where(['name' => $name])->first();
                $isNew = $entity === null;

                $data = [
                    'name' => $name,
                    'kana' => $this->importCell($row, $colKana),
                    'url' => $this->importCell($row, $colUrl),
                    'group_name' => $this->importCell($row, $colGroupName),
                    'note' => $this->importCell($row, $colNote),
                ];

                $salesRankRaw = $this->importCell($row, $colSalesRank);
                $data['sales_rank'] = $this->parseImportSalesRank($salesRankRaw);

                $statusRaw = $this->importCell($row, $colStatus);
                $data['status'] = ctype_digit($statusRaw) ? (int)$statusRaw : $activeStatus;

                if ($isNew) {
                    $data['created_id'] = $authId !== '' ? $authId : '1';
                    $entity = $this->Clients->newEntity($data);
                } else {
                    $data['modified_id'] = $authId !== '' ? $authId : '1';
                    $entity = $this->Clients->patchEntity($entity, $data);
                }

                if (!$this->Clients->save($entity, ['atomic' => false])) {
                    $errors = $entity->getErrors();
                    $isUrlUniqueError = isset($errors['url']['_isUnique']);

                    if ($isUrlUniqueError && !empty($data['url'])) {
                        $data['url'] = '';
                        if ($isNew) {
                            $entity = $this->Clients->newEntity($data);
                        } else {
                            $entity = $this->Clients->patchEntity($entity, $data);
                        }

                        if ($this->Clients->save($entity, ['atomic' => false])) {
                            $urlCleared++;
                            $urlClearedNames[] = $name;
                        } else {
                            throw new \RuntimeException('Import failed: ' . json_encode($entity->getErrors(), JSON_UNESCAPED_UNICODE));
                        }
                    } else {
                        throw new \RuntimeException('Import failed: ' . json_encode($errors, JSON_UNESCAPED_UNICODE));
                    }
                }

                if ($isNew) {
                    $created++;
                } else {
                    $updated++;
                    $updatedNames[] = $name;
                }
            }

            if ($conn->inTransaction()) {
                $conn->commit();
            }
            fclose($handle);

            $details = [];
            if (!empty($updatedNames)) {
                $details[] = __('updated names: {0}', implode(', ', array_values(array_unique($updatedNames))));
            }
            if (!empty($skippedNames)) {
                $details[] = __('skipped names: {0}', implode(', ', array_values(array_unique($skippedNames))));
            }
            if (!empty($urlClearedNames)) {
                $details[] = __('url_cleared names: {0}', implode(', ', array_values(array_unique($urlClearedNames))));
            }

            $message = __('Import completed. created: {0}, updated: {1}, skipped: {2}, url_cleared: {3}', $created, $updated, $skipped, $urlCleared);
            if (!empty($details)) {
                $message .= ' ' . implode(' / ', $details);
            }

            $this->Flash->success($message);
        } catch (\Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollback();
            }
            fclose($handle);
            $this->Flash->error(__('Import failed. {0}', $e->getMessage()));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * @param array<int, mixed> $headerRow
     * @return array<string, int>
     */
    private function buildImportHeaderMap(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $index => $name) {
            $key = $this->normalizeImportHeaderKey((string)$name);
            if ($key !== '') {
                $map[$key] = (int)$index;
            }
        }

        return $map;
    }

    /**
     * @param array<string, int> $headerMap
     * @param array<int, string> $aliases
     */
    private function findImportColumn(array $headerMap, array $aliases): ?int
    {
        foreach ($aliases as $alias) {
            $key = $this->normalizeImportHeaderKey($alias);
            if (isset($headerMap[$key])) {
                return $headerMap[$key];
            }
        }

        return null;
    }

    /**
     * @param array<int, mixed> $row
     */
    private function importCell(array $row, ?int $index): string
    {
        if ($index === null || !array_key_exists($index, $row)) {
            return '';
        }

        return trim((string)$row[$index]);
    }

    private function normalizeImportHeaderKey(string $value): string
    {
        $key = str_replace("\xEF\xBB\xBF", '', $value);
        $key = mb_strtolower(trim($key));
        $key = str_replace([' ', '　'], '', $key);

        return $key;
    }

    private function parseImportSalesRank(string $value): ?int
    {
        $raw = trim($value);
        if ($raw === '') {
            return null;
        }

        if (ctype_digit($raw)) {
            $num = (int)$raw;

            return ($num >= 1 && $num <= 6) ? $num : null;
        }

        $rank = strtolower($raw);
        $map = [
            's' => 1,
            'a' => 2,
            'b' => 3,
            'c' => 4,
            'd' => 5,
            'e' => 6,
        ];

        return $map[$rank] ?? null;
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
