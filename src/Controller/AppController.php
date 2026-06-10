<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->paginate = array_merge([
            'limit' => 50,
            'maxLimit' => 100,
        ], $this->paginate);

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');

        // $this->loadComponent('CakeDC/Users.UsersAuth');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $authenticationConfig = Configure::read('Auth.AuthenticationComponent');
        $this->loadComponent('Authentication.Authentication', $authenticationConfig);
        if (!empty($this->Authentication->getIdentity())) {
            $user = $this->Authentication->getIdentity()->getOriginalData();
            $this->set(compact('user'));
        }
    }

    /**
     * @return array{monthLabel:string,daysInMonth:int,firstWeekday:int,grandTotal:int,byDay:array<int,array{total:int,assignees:array<int,array{name:string,count:int}>}>,assigneeTotals:array<int,array{name:string,count:int}>}
     */
    protected function buildMonthlyClientProposalCalendar(?string $targetMonth = null): array
    {
        [$from, $to, $daysInMonth, $monthLabel, $firstWeekday] = $this->resolveMonthRange($targetMonth);

        $rows = TableRegistry::getTableLocator()->get('ClientProposals')->find()
            ->select(['received_at', 'sender'])
            ->where([
                'ClientProposals.received_at >=' => $from,
                'ClientProposals.received_at <=' => $to,
            ])
            ->enableHydration(false)
            ->toArray();

        $userIds = [];
        foreach ($rows as $row) {
            if (ctype_digit((string)($row['sender'] ?? ''))) {
                $uid = (int)$row['sender'];
                if ($uid > 0) {
                    $userIds[$uid] = $uid;
                }
            }
        }
        $userNameMap = $this->lookupUserNames($userIds);

        $entries = [];
        foreach ($rows as $row) {
            $receivedAt = $row['received_at'] ?? null;
            if ($receivedAt === null) {
                continue;
            }
            $day = (int)(new FrozenTime((string)$receivedAt))->format('j');
            if ($day < 1 || $day > $daysInMonth) {
                continue;
            }
            $uid = ctype_digit((string)($row['sender'] ?? '')) ? (int)$row['sender'] : 0;
            $name = $uid > 0 ? ($userNameMap[$uid] ?? ('User #' . $uid)) : '未設定';
            $entries[] = ['day' => $day, 'userId' => $uid, 'name' => $name];
        }

        return $this->buildCalendarStructure($entries, $daysInMonth, $monthLabel, $firstWeekday);
    }

    /**
     * @return array{monthLabel:string,daysInMonth:int,firstWeekday:int,grandTotal:int,byDay:array<int,array{total:int,assignees:array<int,array{name:string,count:int}>}>,assigneeTotals:array<int,array{name:string,count:int}>}
     */
    protected function buildMonthlyClientProposalBpPicCalendar(?string $targetMonth = null): array
    {
        [$from, $to, $daysInMonth, $monthLabel, $firstWeekday] = $this->resolveMonthRange($targetMonth);

        $rows = TableRegistry::getTableLocator()->get('ClientProposals')->find()
            ->select(['received_at', 'bp_pic_id'])
            ->where([
                'ClientProposals.received_at >=' => $from,
                'ClientProposals.received_at <=' => $to,
                'ClientProposals.bp_pic_id IS NOT' => null,
            ])
            ->enableHydration(false)
            ->toArray();

        $userIds = [];
        foreach ($rows as $row) {
            $uid = (int)($row['bp_pic_id'] ?? 0);
            if ($uid > 0) {
                $userIds[$uid] = $uid;
            }
        }
        $userNameMap = $this->lookupUserNames($userIds);

        $entries = [];
        foreach ($rows as $row) {
            $uid = (int)($row['bp_pic_id'] ?? 0);
            if ($uid <= 0) {
                continue;
            }
            $receivedAt = $row['received_at'] ?? null;
            if ($receivedAt === null) {
                continue;
            }
            $day = (int)(new FrozenTime((string)$receivedAt))->format('j');
            if ($day < 1 || $day > $daysInMonth) {
                continue;
            }
            $entries[] = ['day' => $day, 'userId' => $uid, 'name' => $userNameMap[$uid] ?? ('User #' . $uid)];
        }

        return $this->buildCalendarStructure($entries, $daysInMonth, $monthLabel, $firstWeekday);
    }

    /**
     * @return array{monthLabel:string,daysInMonth:int,firstWeekday:int,grandTotal:int,byDay:array<int,array{total:int,assignees:array<int,array{name:string,count:int}>}>,assigneeTotals:array<int,array{name:string,count:int}>}
     */
    protected function buildMonthlyBpProcurementCalendar(?string $targetMonth = null): array
    {
        [$from, $to, $daysInMonth, $monthLabel, $firstWeekday] = $this->resolveMonthRange($targetMonth);

        $rows = TableRegistry::getTableLocator()->get('BpProcurements')->find()
            ->select(['received_at', 'sender'])
            ->where([
                'BpProcurements.received_at >=' => $from,
                'BpProcurements.received_at <=' => $to,
            ])
            ->enableHydration(false)
            ->toArray();

        $userIds = [];
        foreach ($rows as $row) {
            if (ctype_digit((string)($row['sender'] ?? ''))) {
                $uid = (int)$row['sender'];
                if ($uid > 0) {
                    $userIds[$uid] = $uid;
                }
            }
        }
        $userNameMap = $this->lookupUserNames($userIds);

        $entries = [];
        foreach ($rows as $row) {
            $receivedAt = $row['received_at'] ?? null;
            if ($receivedAt === null) {
                continue;
            }
            $day = (int)(new FrozenTime((string)$receivedAt))->format('j');
            if ($day < 1 || $day > $daysInMonth) {
                continue;
            }
            $uid = ctype_digit((string)($row['sender'] ?? '')) ? (int)$row['sender'] : 0;
            $name = $uid > 0 ? ($userNameMap[$uid] ?? ('User #' . $uid)) : '未設定';
            $entries[] = ['day' => $day, 'userId' => $uid, 'name' => $name];
        }

        return $this->buildCalendarStructure($entries, $daysInMonth, $monthLabel, $firstWeekday);
    }

    /**
     * @return array{monthLabel:string,daysInMonth:int,firstWeekday:int,grandTotal:int,byDay:array<int,array{total:int,assignees:array<int,array{name:string,count:int}>}>,assigneeTotals:array<int,array{name:string,count:int}>}
     */
    protected function buildMonthlyClientBizDevCalendar(?string $targetMonth = null): array
    {
        [$from, $to, $daysInMonth, $monthLabel, $firstWeekday] = $this->resolveMonthRange($targetMonth);

        $rows = TableRegistry::getTableLocator()->get('ClientBusinessDevelopments')->find()
            ->select(['action_at', 'user_id'])
            ->where([
                'ClientBusinessDevelopments.action_at >=' => $from,
                'ClientBusinessDevelopments.action_at <=' => $to,
            ])
            ->enableHydration(false)
            ->toArray();

        $userIds = [];
        foreach ($rows as $row) {
            $uid = (int)($row['user_id'] ?? 0);
            if ($uid > 0) {
                $userIds[$uid] = $uid;
            }
        }
        $userNameMap = $this->lookupUserNames($userIds);

        $entries = [];
        foreach ($rows as $row) {
            $actionAt = $row['action_at'] ?? null;
            if ($actionAt === null) {
                continue;
            }
            $day = (int)(new FrozenTime((string)$actionAt))->format('j');
            if ($day < 1 || $day > $daysInMonth) {
                continue;
            }
            $uid = (int)($row['user_id'] ?? 0);
            $name = $uid > 0 ? ($userNameMap[$uid] ?? ('User #' . $uid)) : '未設定';
            $entries[] = ['day' => $day, 'userId' => $uid, 'name' => $name];
        }

        return $this->buildCalendarStructure($entries, $daysInMonth, $monthLabel, $firstWeekday);
    }

    /**
     * @return array{FrozenTime, FrozenTime, int, string, int}
     */
    private function resolveMonthRange(?string $targetMonth): array
    {
        if ($targetMonth !== null && preg_match('/^\d{4}-\d{2}$/', $targetMonth)) {
            $from = FrozenTime::createFromFormat('Y-m-d', $targetMonth . '-01')->startOfMonth();
        } else {
            $from = FrozenTime::now()->startOfMonth();
        }
        $to = $from->endOfMonth();
        $daysInMonth = (int)$from->format('t');
        $monthLabel = $from->format('Y/m');
        $sundayBased = (int)$from->format('w');
        $mondayBased = ($sundayBased + 6) % 7;

        return [$from, $to, $daysInMonth, $monthLabel, $mondayBased];
    }

    /**
     * @param array<int, int> $userIds
     * @return array<int, string>
     */
    private function lookupUserNames(array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }
        $users = TableRegistry::getTableLocator()->get('Users')->find()
            ->select(['id', 'display_name', 'username'])
            ->where(['id IN' => array_values($userIds)])
            ->enableHydration(false)
            ->toArray();
        $map = [];
        foreach ($users as $user) {
            $name = trim((string)($user['display_name'] ?? ''));
            $map[(int)$user['id']] = $name !== '' ? $name : (string)($user['username'] ?? '');
        }

        return $map;
    }

    /**
     * @param array<int, array{day:int, userId:int, name:string}> $entries
     * @return array{monthLabel:string,daysInMonth:int,firstWeekday:int,grandTotal:int,byDay:array<int,array{total:int,assignees:list<array{name:string,count:int,userId:int}>}>,assigneeTotals:list<array{name:string,count:int,userId:int}>}
     */
    private function buildCalendarStructure(array $entries, int $daysInMonth, string $monthLabel, int $firstWeekday): array
    {
        $byDay = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $byDay[$day] = ['total' => 0, 'assignees' => []];
        }
        $assigneeTotals = [];
        $grandTotal = 0;

        foreach ($entries as $entry) {
            $day = $entry['day'];
            $name = $entry['name'];
            $uid = $entry['userId'];

            $byDay[$day]['total']++;
            if (!isset($byDay[$day]['assignees'][$name])) {
                $byDay[$day]['assignees'][$name] = ['count' => 0, 'userId' => $uid];
            }
            $byDay[$day]['assignees'][$name]['count']++;
            if (!isset($assigneeTotals[$name])) {
                $assigneeTotals[$name] = ['count' => 0, 'userId' => $uid];
            }
            $assigneeTotals[$name]['count']++;
            $grandTotal++;
        }

        $sortFn = static function (array $a, array $b): int {
            return $a['count'] === $b['count'] ? strcmp($a['name'], $b['name']) : $b['count'] <=> $a['count'];
        };

        foreach ($byDay as $day => $data) {
            $assignees = [];
            foreach ($data['assignees'] as $n => $info) {
                $assignees[] = ['name' => (string)$n, 'count' => (int)$info['count'], 'userId' => (int)$info['userId']];
            }
            usort($assignees, $sortFn);
            $byDay[$day]['assignees'] = $assignees;
        }

        $assigneeTotalsList = [];
        foreach ($assigneeTotals as $n => $info) {
            $assigneeTotalsList[] = ['name' => (string)$n, 'count' => (int)$info['count'], 'userId' => (int)$info['userId']];
        }
        usort($assigneeTotalsList, $sortFn);

        return [
            'monthLabel' => $monthLabel,
            'daysInMonth' => $daysInMonth,
            'firstWeekday' => $firstWeekday,
            'grandTotal' => $grandTotal,
            'byDay' => $byDay,
            'assigneeTotals' => $assigneeTotalsList,
        ];
    }

    /**
     * @return array{monthLabel:string,total:int,byAssignee:array<int,array{name:string,count:int}>}
     */
    private function buildMonthlyClientProposalStats(): array
    {
        $from = FrozenTime::now()->startOfMonth();
        $to = FrozenTime::now()->endOfMonth();

        $clientProposals = TableRegistry::getTableLocator()->get('ClientProposals');
        $rows = $clientProposals->find()
            ->select([
                'bp_pic_id',
                'sender',
                'cnt' => $clientProposals->find()->func()->count('*'),
            ])
            ->where([
                'ClientProposals.sales_status' => CLIENT_PROPOSAL_SALES_STATUS_PROPOSING,
                'ClientProposals.received_at >=' => $from,
                'ClientProposals.received_at <=' => $to,
            ])
            ->group(['bp_pic_id', 'sender'])
            ->enableHydration(false)
            ->toArray();

        if (empty($rows)) {
            return [
                'monthLabel' => $from->format('Y/m'),
                'total' => 0,
                'byAssignee' => [],
            ];
        }

        $userIds = [];
        foreach ($rows as $row) {
            $userId = (int)($row['bp_pic_id'] ?? 0);
            if ($userId <= 0 && ctype_digit((string)($row['sender'] ?? ''))) {
                $userId = (int)$row['sender'];
            }
            if ($userId > 0) {
                $userIds[$userId] = $userId;
            }
        }

        $userNameMap = [];
        if (!empty($userIds)) {
            $users = TableRegistry::getTableLocator()->get('Users')->find()
                ->select(['id', 'display_name', 'username'])
                ->where(['id IN' => array_values($userIds)])
                ->enableHydration(false)
                ->toArray();
            foreach ($users as $user) {
                $displayName = trim((string)($user['display_name'] ?? ''));
                $userNameMap[(int)$user['id']] = $displayName !== '' ? $displayName : (string)($user['username'] ?? '');
            }
        }

        $total = 0;
        $byAssigneeMap = [];
        foreach ($rows as $row) {
            $count = (int)($row['cnt'] ?? 0);
            $userId = (int)($row['bp_pic_id'] ?? 0);
            if ($userId <= 0 && ctype_digit((string)($row['sender'] ?? ''))) {
                $userId = (int)$row['sender'];
            }
            $total += $count;
            $name = $userId > 0 ? (string)($userNameMap[$userId] ?? ('User #' . $userId)) : '未設定';
            if (!isset($byAssigneeMap[$name])) {
                $byAssigneeMap[$name] = 0;
            }
            $byAssigneeMap[$name] += $count;
        }

        $byAssignee = [];
        foreach ($byAssigneeMap as $name => $count) {
            $byAssignee[] = [
                'name' => (string)$name,
                'count' => (int)$count,
            ];
        }

        usort($byAssignee, static function (array $a, array $b): int {
            return $b['count'] <=> $a['count'];
        });

        return [
            'monthLabel' => $from->format('Y/m'),
            'total' => $total,
            'byAssignee' => $byAssignee,
        ];
    }
}
