<?php
namespace AfriCC\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report;

class PaymentFunds extends Report
{
    public function setAccountType($accountType)
    {
        $this->set('extreport:paymentFunds/extreport:accountType', $accountType);
    }
}

