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
    }
}
