<?php
namespace AfriCC\EPP\Extension\NASK\Transfer;

use \AfriCC\EPP\Frame\Command\Transfer\Domain as DomainTransfer;
use \AfriCC\EPP\ExtensionInterface as Extension;


class Domain extends DomainTransfer implements Extension
{
    protected $extension = 'extdom';
    protected $extension_xmlns = 'http://www.dns.pl/nask-epp-schema/extdom-2.0';
    
    public function getExtensionName()
    {
        return $this->extension;
    }
    
    public function getExtensionNamespace()
    {
        return $this->extension_xmlns;
    }
    
    public function resendConfirmationRequest(){
        $this->set('//epp:epp/epp:command/epp:extension/extdom:transfer/extdom:resendConfirmationRequest');
    }

}

