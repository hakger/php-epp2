<?php
namespace AfriCC\EPP\Extension\NASK\Update;

use AfriCC\EPP\ExtensionInterface;
use AfriCC\EPP\Frame\Command\Update\Contact as ContactUpdate;

class Contact extends ContactUpdate implements ExtensionInterface
{
    
    protected $extension = 'extcon';
    protected $extension_xmlns = 'http://www.dns.pl/nask-epp-schema/extcon-2.0';
    
    public function getExtensionNamespace()
    {
        return $this->extension_xmlns;
    }
    
    public function getExtensionName()
    {
        return $this->extension;
    }

    
    public function setConsentForPublishing($consent=false){
        $this->set('//epp:epp/epp:command/epp:extension/extcon:update/extcon:consentForPublishing', $consent ? 1 : 0 );
    }

}

