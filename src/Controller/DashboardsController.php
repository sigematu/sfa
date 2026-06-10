<?php
declare(strict_types=1);

namespace App\Controller;

class DashboardsController extends AppController
{
    public function index()
    {
        $month = trim((string)$this->request->getQuery('month'));
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = null;
        }
        $this->set('calClientProposal',      $this->buildMonthlyClientProposalCalendar($month));
        $this->set('calClientProposalBpPic', $this->buildMonthlyClientProposalBpPicCalendar($month));
        $this->set('calBpProcurement',       $this->buildMonthlyBpProcurementCalendar($month));
        $this->set('calClientBizDev',        $this->buildMonthlyClientBizDevCalendar($month));
        $this->set('currentMonth',           $month ?? date('Y-m'));

        $clientProposalSections = [
            ['field' => 'evaluation',   'title' => '評価',     'labels' => CLIENT_PROPOSAL_EVALUATION_LABELS],
            ['field' => 'sales_status', 'title' => '営業状況', 'labels' => CLIENT_PROPOSAL_SALES_STATUS_LABELS],
            ['field' => 'sales_reason', 'title' => '事由',     'labels' => CLIENT_PROPOSAL_REASON_LABELS],
        ];
        $bpProcurementSections = [
            ['field' => 'evaluation',   'title' => '評価',     'labels' => BP_PROCUREMENT_EVALUATION_LABELS],
            ['field' => 'sales_status', 'title' => '営業状況', 'labels' => BP_PROCUREMENT_STATUS_LABELS],
            ['field' => 'sales_reason', 'title' => '事由',     'labels' => BP_PROCUREMENT_REASON_LABELS],
        ];
        $clientBizDevSections = [
            ['field' => 'sales_status', 'title' => '営業状況', 'labels' => CLIENT_BIZ_DEV_SALES_STATUS_LABELS],
            ['field' => 'sales_reason', 'title' => '事由',     'labels' => CLIENT_BIZ_DEV_REASON_LABELS],
        ];

        $this->set('summaryClientProposal', $this->buildMonthlyStatusSummaryTabs(
            'ClientProposals',
            'received_at',
            'sender',
            $clientProposalSections,
            $month
        ));
        $this->set('summaryClientProposalBpPic', $this->buildMonthlyStatusSummaryTabs(
            'ClientProposals',
            'received_at',
            'bp_pic_id',
            $clientProposalSections,
            $month,
            ['ClientProposals.bp_pic_id IS NOT' => null]
        ));
        $this->set('summaryBpProcurement', $this->buildMonthlyStatusSummaryTabs(
            'BpProcurements',
            'received_at',
            'sender',
            $bpProcurementSections,
            $month
        ));
        $this->set('summaryClientBizDev', $this->buildMonthlyStatusSummaryTabs(
            'ClientBusinessDevelopments',
            'action_at',
            'user_id',
            $clientBizDevSections,
            $month
        ));
    }
}
