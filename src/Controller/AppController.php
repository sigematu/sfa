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
     * 指定テーブル・月のステータス別件数/割合サマリを、全体＋担当者別タブで作成する。
     *
     * @param string $tableName 対象テーブル名（エイリアス）
     * @param string $dateField 月絞り込みに使う日時カラム
     * @param string $assigneeField 担当者を表すカラム（数値ならユーザID）
     * @param array<int, array{field:string, title:string, labels:array<int,string>}> $sections 集計対象セクション
     * @param string|null $targetMonth 対象月（Y-m）
     * @param array<string, mixed> $extraConditions 追加の絞り込み条件
     * @return array<int, array{key:string, name:string, total:int, sections:array<int, array{title:string, total:int, rows:array<int, array{label:string, count:int, percentage:float}>}>}>
     */
    protected function buildMonthlyStatusSummaryTabs(
        string $tableName,
        string $dateField,
        string $assigneeField,
        array $sections,
        ?string $targetMonth = null,
        array $extraConditions = []
    ): array {
        [$from, $to] = $this->resolveMonthRange($targetMonth);

        $selectFields = [$assigneeField => $assigneeField];
        foreach ($sections as $section) {
            $selectFields[$section['field']] = $section['field'];
        }

        $conditions = [
            $tableName . '.' . $dateField . ' >=' => $from,
            $tableName . '.' . $dateField . ' <=' => $to,
        ] + $extraConditions;

        $rows = TableRegistry::getTableLocator()->get($tableName)->find()
            ->select(array_values($selectFields))
            ->where($conditions)
            ->enableHydration(false)
            ->toArray();

        // 担当者ID（数値）を収集して名前解決
        $userIds = [];
        foreach ($rows as $row) {
            $raw = (string)($row[$assigneeField] ?? '');
            if (ctype_digit($raw) && (int)$raw > 0) {
                $userIds[(int)$raw] = (int)$raw;
            }
        }
        $userNameMap = $this->lookupUserNames($userIds);

        // 担当者ごとに行をグループ化
        $groups = [];
        foreach ($rows as $row) {
            $raw = (string)($row[$assigneeField] ?? '');
            if (ctype_digit($raw) && (int)$raw > 0) {
                $key = $raw;
                $name = $userNameMap[(int)$raw] ?? ('User #' . $raw);
            } else {
                $key = 'unset';
                $name = '未設定';
            }
            if (!isset($groups[$key])) {
                $groups[$key] = ['name' => $name, 'rows' => []];
            }
            $groups[$key]['rows'][] = $row;
        }

        // 件数降順で担当者を並べ替え
        uasort($groups, static fn(array $a, array $b): int => count($b['rows']) <=> count($a['rows']));

        // 全体タブを先頭に、続けて担当者タブ
        $tabs = [[
            'key' => 'all',
            'name' => '全体',
            'total' => count($rows),
            'sections' => $this->computeStatusSections($rows, $sections),
        ]];
        foreach ($groups as $key => $group) {
            $tabs[] = [
                'key' => (string)$key,
                'name' => $group['name'],
                'total' => count($group['rows']),
                'sections' => $this->computeStatusSections($group['rows'], $sections),
            ];
        }

        return $tabs;
    }

    /**
     * 行集合をセクション定義に従って件数/割合に集計する。
     *
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, array{field:string, title:string, labels:array<int,string>}> $sections
     * @return array<int, array{title:string, total:int, rows:array<int, array{label:string, count:int, percentage:float}>}>
     */
    private function computeStatusSections(array $rows, array $sections): array
    {
        $result = [];
        foreach ($sections as $section) {
            $field = $section['field'];
            $labels = $section['labels'];
            $hasUnsetLabel = array_key_exists(0, $labels);

            $counts = array_fill_keys(array_keys($labels), 0);
            $extraUnset = 0;
            $total = 0;
            foreach ($rows as $row) {
                $raw = $row[$field] ?? null;
                $key = ($raw === null || $raw === '' || !ctype_digit((string)$raw)) ? 0 : (int)$raw;
                if (array_key_exists($key, $counts)) {
                    $counts[$key]++;
                } elseif ($hasUnsetLabel) {
                    $counts[0]++;
                } else {
                    $extraUnset++;
                }
                $total++;
            }

            $rowsOut = [];
            foreach ($labels as $value => $label) {
                $count = $counts[$value];
                $rowsOut[] = [
                    'label' => $label,
                    'count' => $count,
                    'percentage' => $total > 0 ? ($count / $total * 100) : 0.0,
                ];
            }
            if (!$hasUnsetLabel && $extraUnset > 0) {
                $rowsOut[] = [
                    'label' => '未設定',
                    'count' => $extraUnset,
                    'percentage' => $total > 0 ? ($extraUnset / $total * 100) : 0.0,
                ];
            }

            $result[] = ['title' => $section['title'], 'total' => $total, 'rows' => $rowsOut];
        }

        return $result;
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
