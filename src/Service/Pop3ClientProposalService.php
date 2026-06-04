<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Table\ClientProposalsTable;
use Cake\I18n\FrozenTime;

class Pop3ClientProposalService
{
    private ClientProposalsTable $clientProposals;

    /** @var array<string, mixed> */
    private array $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(ClientProposalsTable $clientProposals, array $config = [])
    {
        $this->clientProposals = $clientProposals;
        $this->config = $config;
    }

    public function import(): int
    {
        if (!function_exists('imap_open')) {
            throw new \RuntimeException(__('POP3 extension (imap) is not enabled.'));
        }

        $host = (string)($this->config['host'] ?? '');
        $port = (int)($this->config['port'] ?? 995);
        $username = (string)($this->config['username'] ?? '');
        $password = (string)($this->config['password'] ?? '');
        $ssl = (bool)($this->config['ssl'] ?? true);
        $novalidateCert = (bool)($this->config['novalidate_cert'] ?? true);
        $maxMessages = (int)($this->config['max_messages'] ?? 100);

        if ($host === '' || $username === '' || $password === '') {
            throw new \RuntimeException(__('POP3 settings are not configured.'));
        }

        $mailbox = sprintf(
            '{%s:%d/pop3%s%s}INBOX',
            $host,
            $port,
            $ssl ? '/ssl' : '',
            $novalidateCert ? '/novalidate-cert' : ''
        );

        $inbox = @imap_open($mailbox, $username, $password);
        if ($inbox === false) {
            throw new \RuntimeException(__('Failed to connect to POP3 server.'));
        }

        try {
            $uids = imap_search($inbox, 'ALL', SE_UID);
            if (empty($uids)) {
                return 0;
            }

            rsort($uids);
            $uids = array_slice($uids, 0, $maxMessages);

            $imported = 0;
            foreach ($uids as $uid) {
                $uidString = (string)$uid;

                $exists = $this->clientProposals->find()
                    ->where(['message_uid' => $uidString])
                    ->count();
                if ($exists > 0) {
                    continue;
                }

                $msgNo = imap_msgno($inbox, (int)$uid);
                if ($msgNo <= 0) {
                    continue;
                }

                $overviewList = imap_fetch_overview($inbox, (string)$uid, FT_UID);
                $overview = $overviewList[0] ?? null;

                $subject = $this->decodeMime($overview->subject ?? '');
                $sender = $this->decodeMime($overview->from ?? '');
                $recipient = $this->decodeMime($overview->to ?? '');
                $receivedAt = !empty($overview->date)
                    ? new FrozenTime((string)$overview->date)
                    : null;

                $bodyText = trim((string)imap_fetchbody($inbox, $msgNo, '1'));
                if ($bodyText === '') {
                    $bodyText = trim((string)imap_body($inbox, $msgNo));
                }

                $bodyHtml = trim((string)imap_fetchbody($inbox, $msgNo, '2'));
                $headers = (string)imap_fetchheader($inbox, $msgNo);

                $entity = $this->clientProposals->newEntity([
                    'message_uid' => $uidString,
                    'received_at' => $receivedAt,
                    'sender' => $sender,
                    'recipient' => $recipient,
                    'subject' => $subject,
                    'body_text' => $bodyText,
                    'body_html' => $bodyHtml,
                    'headers' => $headers,
                ]);

                if ($this->clientProposals->save($entity)) {
                    $imported++;
                }
            }

            return $imported;
        } finally {
            imap_close($inbox);
        }
    }

    private function decodeMime(string $value): string
    {
        $decoded = imap_mime_header_decode($value);
        $parts = [];
        foreach ($decoded as $part) {
            $text = (string)($part->text ?? '');
            $charset = strtoupper((string)($part->charset ?? 'UTF-8'));
            if ($charset !== 'DEFAULT' && $charset !== 'UTF-8') {
                $converted = @mb_convert_encoding($text, 'UTF-8', $charset);
                $parts[] = $converted !== false ? $converted : $text;
            } else {
                $parts[] = $text;
            }
        }

        return trim(implode('', $parts));
    }
}
