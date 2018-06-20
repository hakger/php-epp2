<?php
namespace AfriCC\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report;

class Payment extends Report
{
    public function setAccountType($accountType)
    {
        $this->set('extreport:payment/extreport:accountType', $accountType);
    }
}

