<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\Pop3ClientProposalService;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * ClientProposals Controller
 *
 * @property \App\Model\Table\ClientProposalsTable $ClientProposals
 */
class ClientProposalsController extends AppController
{
    public $paginate = [
        'order' => ['ClientProposals.received_at' => 'DESC', 'ClientProposals.id' => 'DESC'],
        'limit' => 50,
    ];

    public function index()
    {
        if ($this->request->getQuery('sync') === '1') {
            $this->syncMailbox();

            return $this->redirect(['action' => 'index']);
        }

        $query = $this->ClientProposals->find();
        $clientProposals = $this->paginate($query);

        $recipientEmails = [];
        foreach ($clientProposals as $proposal) {
            foreach ($this->extractEmails((string)$proposal->recipient) as $email) {
                $recipientEmails[$email] = true;
            }
        }

        $contactByEmail = [];
        if (!empty($recipientEmails)) {
            $emails = array_keys($recipientEmails);
            $clientContactsTable = TableRegistry::getTableLocator()->get('ClientContacts');
            $contacts = $clientContactsTable->find()
                ->select(['id', 'client_id', 'name', 'email'])
                ->where(['ClientContacts.email IN' => $emails])
                ->contain(['Clients' => function ($q) {
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

        $proposalContactMap = [];
        foreach ($clientProposals as $proposal) {
            foreach ($this->extractEmails((string)$proposal->recipient) as $email) {
                if (isset($contactByEmail[$email])) {
                    $proposalContactMap[$proposal->id] = $contactByEmail[$email];
                    break;
                }
            }
        }

        $this->set(compact('clientProposals', 'proposalContactMap'));
    }

    public function view($id = null)
    {
        $clientProposal = $this->ClientProposals->get($id);
        $matchedContact = $this->findContactByRecipient((string)$clientProposal->recipient);

        $this->set(compact('clientProposal', 'matchedContact'));
    }

    private function syncMailbox(): void
    {
        $service = new Pop3ClientProposalService($this->ClientProposals, (array)Configure::read('Pop3ClientProposal', []));

        try {
            $count = $service->import();
            $this->Flash->success(__('Mail sync completed. {0} new message(s) imported.', $count));
        } catch (\Throwable $e) {
            Log::error('Client proposal POP3 sync failed: ' . $e->getMessage());
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

        $clientContactsTable = TableRegistry::getTableLocator()->get('ClientContacts');

        return $clientContactsTable->find()
            ->select(['id', 'client_id', 'name', 'email'])
            ->where(['ClientContacts.email IN' => $emails])
            ->contain(['Clients' => function ($q) {
                return $q->select(['id', 'name']);
            }])
            ->first();
    }
}
