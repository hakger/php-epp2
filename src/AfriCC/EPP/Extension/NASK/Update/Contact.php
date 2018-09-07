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

    /**
     * Warning: may not work!
     * @param boolean $individual
     */
    public function setIndividual($individual=false){
        $this->set('//epp:epp/epp:command/epp:extension/extcon:update/extcon:individual', $individual ? 1 : 0);
    }
    
    public function setConsentForPublishing($consent=false){
        $this->set('//epp:epp/epp:command/epp:extension/extcon:update/extcon:consentForPublishing', $consent ? 1 : 0 );
    }

}

