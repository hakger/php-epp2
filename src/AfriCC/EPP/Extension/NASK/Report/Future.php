<?php
namespace AfriCC\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report;

class Future extends Report
{
    public function __construct()
    {
        parent::__construct();
        $this->set('extreport:future');
    }

    public function setExDate($exDate)
    {
        $this->set('extreport:future/extreport:name', $exDate);
    }
}

