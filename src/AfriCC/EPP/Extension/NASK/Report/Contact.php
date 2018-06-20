<?php
namespace AfriCC\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report;

class Contact extends Report
{
    public function __construct()
    {
        parent::__construct();
        $this->set('extreport:contact');
    }
    
    public function setContactId($contact)
    {
        $this->set('extreport:contact/extreport:conId', $contact);
    }
}

