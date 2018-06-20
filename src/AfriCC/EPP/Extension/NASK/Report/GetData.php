<?php
namespace AfriCC\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report;

class GetData extends Report
{
    public function setReportId($reportId)
    {
        $this->set('extreport:getData/extreport:extreportId', $reportId);
    }
}

