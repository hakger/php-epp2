<?php
namespace AfriCC\EPP\Extension\NASK;

use AfriCC\EPP\ObjectSpec as MainObjectSpec;

class ObjectSpec extends MainObjectSpec
{

    public static $specs = [
        'epp' => [
            'xmlns' => 'http://www.dns.pl/nask-epp-schema/epp-2.0',
        ],
        'domain' => [
            'xmlns' => 'http://www.dns.pl/nask-epp-schema/domain-2.0',
        ],
        'host' => [
            'xmlns' => 'http://www.dns.pl/nask-epp-schema/host-2.0'
        ],
        'contact' => [
            'xmlns' => 'http://www.dns.pl/nask-epp-schema/contact-2.0'
        ],
        'future' => [
            'xmlns' => 'http://www.dns.pl/nask-epp-schema/future-2.0'
        ],
    
    ];
    
    public static $services = [
        'http://www.dns.pl/nask-epp-schema/contact-2.0',
        'http://www.dns.pl/nask-epp-schema/host-2.0',
        'http://www.dns.pl/nask-epp-schema/domain-2.0',
        'http://www.dns.pl/nask-epp-schema/future-2.0',
    ];
    
    public static $serviceExtensions = [
        'http://www.dns.pl/nask-epp-schema/extcon-2.0',
        'http://www.dns.pl/nask-epp-schema/extdom-2.0',
        'http://www.dns.pl/nask-epp-schema/secDNS-2.0',
    ];
    
    public static function overwriteParent(){
        MainObjectSpec::$specs = self::$specs;
    }
    
    
}

