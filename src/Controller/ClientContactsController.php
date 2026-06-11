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
        $email = trim((string)$this->request->getQuery('email'));
        $clients = $this->ClientContacts->Clients->find('list', ['order' => ['kana' => 'ASC']])->all();
        $categories = CLIENT_CONTACT_CATEGORY_LABELS;
        $inactiveReasons = CLIENT_CONTACT_INACTIVE_REASON_LABELS;
        $locations = CLIENT_CONTACT_LOCATION_LABELS;
        $this->set(compact('clientContact', 'clients', 'client_id', 'email', 'categories', 'inactiveReasons', 'locations', 'hierarchyOptionsByClient'));
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
        $locations = CLIENT_CONTACT_LOCATION_LABELS;
        $this->set(compact('clientContact', 'clients', 'categories', 'inactiveReasons', 'locations', 'hierarchyOptionsByClient'));
    }

    /**
     * Import method (CSV)
     *
     * @return \Cake\Http\Response|null
     */
    public function import()
    {
        $this->request->allowMethod(['post']);

        $role = (string)$this->request->getSession()->read('Auth.role');
        if ($role !== \App\Model\Table\UsersTable::ROLE_ADMIN) {
            $this->Flash->error(__('You are not authorized to import client contacts.'));

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

        $csv = (string)$upload->getStream();
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
        $colCompany = $this->findImportColumn($headerMap, ['company', 'client', 'company_name', 'client_name', '会社名', '顧客名', '顧客']);
        $colName = $this->findImportColumn($headerMap, ['name', 'contact_name', 'client_contact', '担当者名', '顧客担当者', '氏名']);

        if ($colCompany === null || $colName === null) {
            fclose($handle);
            $this->Flash->error(__('Required headers are missing: company and/or name'));

            return $this->redirect(['action' => 'index']);
        }

        $colKana = $this->findImportColumn($headerMap, ['kana', 'contact_kana', '担当者カナ', 'カナ']);
        $colEmail = $this->findImportColumn($headerMap, ['email', 'mail', 'メールアドレス']);
        $colMobile = $this->findImportColumn($headerMap, ['mobile_phone', 'mobile', '携帯電話', '携帯番号']);
        $colLandline = $this->findImportColumn($headerMap, ['landline_phone', 'landline', '固定電話', '電話番号', 'tel']);
        $colDepartment = $this->findImportColumn($headerMap, ['department', '部署']);
        $colPosition = $this->findImportColumn($headerMap, ['position', '役職']);
        $colCategory = $this->findImportColumn($headerMap, ['category', 'responsible_area', '担当領域']);
        $colRole = $this->findImportColumn($headerMap, ['role', '役割']);
        $colHierarchy = $this->findImportColumn($headerMap, ['hierarchy', '上位者', '階層']);
        $colStatus = $this->findImportColumn($headerMap, ['status', 'ステータス']);
        $colMailDelivery = $this->findImportColumn($headerMap, ['mail_delivery', 'メール配信']);
        $colAreaOnlyDelivery = $this->findImportColumn($headerMap, ['area_only_delivery', '担当領域のみ配信']);
        $colInactiveReason = $this->findImportColumn($headerMap, ['inactive_reason', '無効理由']);
        $colNote = $this->findImportColumn($headerMap, ['note', '備考']);

        $clientsByName = [];
        foreach ($this->ClientContacts->Clients->find()->select(['id', 'name'])->all() as $client) {
            $clientsByName[$this->normalizeImportName((string)$client->name)] = (int)$client->id;
        }

        $authId = (string)$this->request->getSession()->read('Auth.id');
        $activeStatus = defined('STATUS_ACTIVE') ? (int)STATUS_ACTIVE : 1;
        $conn = $this->ClientContacts->getConnection();
        $conn->begin();

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $skippedNames = [];
        $csvRowNumber = 1;

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $csvRowNumber++;
                $companyName = $this->importCell($row, $colCompany);
                $contactName = $this->importCell($row, $colName);
                if ($companyName === '' || $contactName === '') {
                    $skipped++;
                    $skippedNames[] = $contactName !== '' ? $contactName : __('(empty row)');
                    continue;
                }

                $clientId = $clientsByName[$this->normalizeImportName($companyName)] ?? null;
                if ($clientId === null) {
                    $skipped++;
                    $skippedNames[] = $contactName . ' [' . $companyName . ']';
                    continue;
                }

                $email = $this->importCell($row, $colEmail);
                $entity = null;
                if ($email !== '') {
                    $entity = $this->ClientContacts->find()->where(['client_id' => $clientId, 'email' => $email])->first();
                }
                if ($entity === null) {
                    $entity = $this->ClientContacts->find()->where(['client_id' => $clientId, 'name' => $contactName])->first();
                }

                $isNew = $entity === null;
                $data = [
                    'client_id' => $clientId,
                    'name' => $contactName,
                    'kana' => $this->importCell($row, $colKana),
                    'email' => $email,
                    'mobile_phone' => $this->importCell($row, $colMobile),
                    'landline_phone' => $this->importCell($row, $colLandline),
                    'department' => $this->nullIfEmpty($this->importCell($row, $colDepartment)),
                    'position' => $this->parseImportMappedValue($this->importCell($row, $colPosition), $this->getPositionLabels()),
                    'category' => $this->parseImportMappedValue($this->importCell($row, $colCategory), CLIENT_CONTACT_CATEGORY_LABELS),
                    'role' => $this->parseImportMappedValue($this->importCell($row, $colRole), CLIENT_CONTACT_ROLE_LABELS),
                    'hierarchy' => null,
                    'status' => $this->parseImportStatus($this->importCell($row, $colStatus), $activeStatus),
                    'mail_delivery' => $this->parseImportBooleanInt($this->importCell($row, $colMailDelivery), 0),
                    'area_only_delivery' => $this->parseImportBooleanInt($this->importCell($row, $colAreaOnlyDelivery), 0),
                    'inactive_reason' => $this->parseImportMappedValue($this->importCell($row, $colInactiveReason), CLIENT_CONTACT_INACTIVE_REASON_LABELS),
                    'note' => $this->importCell($row, $colNote),
                ];

                $hierarchyName = $this->importCell($row, $colHierarchy);
                if ($hierarchyName !== '') {
                    $parent = $this->ClientContacts->find()
                        ->select(['id'])
                        ->where(['client_id' => $clientId, 'name' => $hierarchyName])
                        ->first();
                    $data['hierarchy'] = $parent ? (int)$parent->id : null;
                }

                $data = $this->normalizeClientContactOptionalFields($data, $this->getHierarchyOptionsByClient($isNew ? null : (int)$entity->id), $isNew ? null : (int)$entity->id);

                if ($isNew) {
                    $data['created_id'] = $authId !== '' ? $authId : '1';
                    $entity = $this->ClientContacts->newEntity($data);
                } else {
                    $data['modified_id'] = $authId !== '' ? $authId : '1';
                    $entity = $this->ClientContacts->patchEntity($entity, $data);
                }

                if (!$this->ClientContacts->save($entity, ['atomic' => false])) {
                    $errorSummary = $this->formatImportValidationErrors((array)$entity->getErrors());
                    $rowSummary = $this->formatImportRowSummary($csvRowNumber, [
                        'company' => $companyName,
                        'name' => $contactName,
                        'email' => $email,
                        'mobile_phone' => (string)($data['mobile_phone'] ?? ''),
                        'landline_phone' => (string)($data['landline_phone'] ?? ''),
                    ]);
                    throw new \RuntimeException($rowSummary . ' errors: ' . $errorSummary);
                }

                if ($isNew) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            if ($conn->inTransaction()) {
                $conn->commit();
            }
            fclose($handle);

            $message = __('Import completed. created: {0}, updated: {1}, skipped: {2}', $created, $updated, $skipped);
            if (!empty($skippedNames)) {
                $message .= ' ' . __('skipped names: {0}', implode(', ', array_values(array_unique($skippedNames))));
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

        $positionTitle = trim((string)($data['position_title'] ?? ''));
        $data['position_title'] = $positionTitle === '' ? null : $positionTitle;

        $data['category'] = isset(CLIENT_CONTACT_CATEGORY_LABELS[(int)($data['category'] ?? 0)])
            ? (int)$data['category']
            : null;

        $data['role'] = isset(CLIENT_CONTACT_ROLE_LABELS[(int)($data['role'] ?? 0)])
            ? (int)$data['role']
            : null;

        $data['location'] = isset(CLIENT_CONTACT_LOCATION_LABELS[(int)($data['location'] ?? 0)])
            ? (int)$data['location']
            : null;

        $base = trim((string)($data['base'] ?? ''));
        $data['base'] = $base === '' ? null : $base;

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

    private function normalizeImportName(string $value): string
    {
        $normalized = mb_strtolower(trim($value));
        $normalized = str_replace(['株式会社', '合同会社', '有限会社', ' ', '　'], '', $normalized);

        return $normalized;
    }

    /**
     * @param array<int|string, string> $labels
     */
    private function parseImportMappedValue(string $value, array $labels): ?int
    {
        $raw = trim($value);
        if ($raw === '') {
            return null;
        }

        if (ctype_digit($raw)) {
            return (int)$raw;
        }

        $normalized = $this->normalizeImportName($raw);
        foreach ($labels as $key => $label) {
            if ($this->normalizeImportName((string)$label) === $normalized) {
                return (int)$key;
            }
        }

        return null;
    }

    private function parseImportStatus(string $value, int $default): int
    {
        $raw = trim($value);
        if ($raw === '') {
            return $default;
        }

        if (ctype_digit($raw)) {
            return (int)$raw;
        }

        $normalized = mb_strtolower($raw);
        if (in_array($normalized, ['active', '有効', 'on', 'true'], true)) {
            return STATUS_ACTIVE;
        }
        if (in_array($normalized, ['inactive', '無効', 'off', 'false'], true)) {
            return STATUS_INACTIVE;
        }

        return $default;
    }

    private function parseImportBooleanInt(string $value, int $default): int
    {
        $raw = trim($value);
        if ($raw === '') {
            return $default;
        }

        if (ctype_digit($raw)) {
            return (int)$raw;
        }

        $normalized = mb_strtolower($raw);
        if (in_array($normalized, ['yes', 'y', 'true', 'on', '有効'], true)) {
            return 1;
        }
        if (in_array($normalized, ['no', 'n', 'false', 'off', '無効'], true)) {
            return 0;
        }

        return $default;
    }

    private function nullIfEmpty(string $value): ?string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * @return array<int, string>
     */
    private function getPositionLabels(): array
    {
        return [
            POS_CEO => '社長・代表',
            POS_EXECUTIVE => '役員級',
            POS_DEPARTMENT_HEAD => '部長級',
            POS_SECTION_MANAGER => '次長・課長級',
            POS_TEAM_LEADER => '主任級',
            POS_STAFF => '一般職',
        ];
    }

    /**
     * @param array<string, mixed> $errors
     */
    private function formatImportValidationErrors(array $errors): string
    {
        $messages = [];
        foreach ($errors as $field => $fieldErrors) {
            if (!is_array($fieldErrors)) {
                $messages[] = $field . ': ' . (string)$fieldErrors;
                continue;
            }

            foreach ($fieldErrors as $message) {
                if (is_string($message) && $message !== '') {
                    $messages[] = $field . ': ' . $message;
                }
            }
        }

        if (empty($messages)) {
            return 'unknown validation error';
        }

        return implode(' | ', $messages);
    }

    /**
     * @param array<string, string> $rowContext
     */
    private function formatImportRowSummary(int $csvRowNumber, array $rowContext): string
    {
        $parts = ['csv_row=' . $csvRowNumber];
        foreach ($rowContext as $key => $value) {
            if ($value === '') {
                continue;
            }
            $parts[] = $key . '="' . $value . '"';
        }

        return implode(', ', $parts) . '.';
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
