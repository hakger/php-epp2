<?php
namespace AfriCC\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report;

class Host extends Report
{
    public function __construct()
    {
        parent::__construct();
        $this->set('extreport:host');
    }

    public function setName($name)
    {
        $this->set('extreport:host/extreport:name', $name);
    }
}

