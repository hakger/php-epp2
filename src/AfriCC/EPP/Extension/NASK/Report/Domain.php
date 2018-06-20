<?php
namespace AfriCC\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report;

class Domain extends Report
{
    public function __construct()
    {
        parent::__construct();
        $this->set('extreport:domain');
    }
    
    public function setState($state)
    {
        $this->set('extreport:domain/extreport:state', $state);    
    }
    
    public function setExDate($date)
    {
        $this->set('extreport:domain/extreport:exDate', $date);
    }
    
    public function addStatus($status)
    {
        $this->set('extreport:domain/extreport:statuses/extreport:status[]', $status);
    }
    
    public function setStatusesIn($statusesIn = true)
    {
        $node = $this->set('extreport:domain/extreport:statuses');
        
        $node->setAttribute('statusesIn', ($statusesIn) ? 'true':'false');
    }
}

