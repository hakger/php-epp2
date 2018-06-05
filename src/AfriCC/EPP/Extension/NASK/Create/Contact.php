<?php
namespace AfriCC\EPP\Extension\NASK\Create;

use AfriCC\EPP\ExtensionInterface;
use AfriCC\EPP\Frame\Command\Create\Contact as ContactCreate;

class Contact extends ContactCreate implements ExtensionInterface
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
    
    public function setIndividual($individual=false){
        $this->set('//epp:epp/epp:command/epp:extension/extcon:create/extcon:individual', $individual ? 1 : 0);
    }
    
    public function setConsentForPublishing($consent=false){
        $this->set('//epp:epp/epp:command/epp:extension/extcon:create/extcon:consentForPublishing', $consent ? 1 : 0 );
    }
}

