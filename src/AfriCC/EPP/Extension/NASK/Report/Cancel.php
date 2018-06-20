<?php
namespace AfriCC\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report;

class Cancel extends Report
{
    public function setReportId($reportId)
    {
        $this->set('extreport:cancel/extreport:extreportId', $reportId);
    }
}

