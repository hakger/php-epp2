<?php
namespace AfriCC\EPP\Extension\NASK\Create;

use AfriCC\EPP\ExtensionInterface;
use AfriCC\EPP\Frame\Command\Create\Domain as DomainCreate;

class Domain extends DomainCreate implements ExtensionInterface
{

    protected $extension= 'extdom';
    
    protected $extension_xmlns='http://www.dns.pl/nask-epp-schema/extdom-2.0';
    
    public function getExtensionNamespace()
    {
        return $this->extension;
    }

    public function getExtensionName()
    {
        return $this->extension_xmlns;
    }
    
    public function setBook()
    {
        $this->set('//epp:epp/epp:command/epp:extension/extdom:create/extdom:book');
    }
    
    public function setReason($reason)
    {
        $this->set('//epp:epp/epp:command/epp:extension/extdom:create/extdom:reason', $reason);
    }
}

