<?php
namespace AfriCC\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report;

class Prepaid extends Report
{
    
    private function isAccountType($accountType)
    {
        switch (strtoupper($accountType)){
            case 'DOMAIN':
            case 'ENUM':
                return true;
            default:
                return false;
        }
    }
    
    public function setPaymentsAccountType($accountType)
    {
        if(!$this->isAccountType($accountType)){
            throw new \Exception(sprintf('"%s" is not valid Account Type!', $accountType));
        }
        $this->set('extreport:prepaid/extreport:payment/extreport:accountType', $accountType);
    }
    
    public function setFundsAccountType($accountType)
    {
        if(!$this->isAccountType($accountType)){
            throw new \Exception(sprintf('"%s" is not valid Account Type!', $accountType));
        }
        $this->set('extreport:prepaid/extreport:paymentFunds/extreport:accountType', $accountType);
    }
    
    
}

