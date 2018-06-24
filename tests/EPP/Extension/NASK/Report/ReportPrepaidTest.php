<?php
namespace AfriCC\Tests\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report\Prepaid as Report;
use AfriCC\EPP\Extension\NASK\ObjectSpec;
use PHPUnit\Framework\TestCase;

/**
 * @backupStaticAttributes enabled
 */
class ReportPrepaidTest extends TestCase
{
    public function setUp()
    {
        ObjectSpec::overwriteParent();
    }
    
    public function tearDown()
    {
        ObjectSpec::restoreParent();
    }
    
    public function testNaskPrepaidPayments()
    {
        //ObjectSpec::overwriteParent();
        $frame = new Report();
        $frame->setPaymentsAccountType('domain');
        $frame->setOffset(0);
        $frame->setLimit(50);
        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8"?>
<epp xmlns="http://www.dns.pl/nask-epp-schema/epp-2.0">
<extension>
<extreport:report
xmlns:extreport="http://www.dns.pl/nask-epp-schema/extreport-2.0">
<extreport:prepaid>
<extreport:payment>
<extreport:accountType>domain</extreport:accountType>
</extreport:payment>
</extreport:prepaid>
<extreport:offset>0</extreport:offset>
<extreport:limit>50</extreport:limit>
</extreport:report>
</extension>
</epp>
            ',
            (string) $frame
            );
    }
    
    public function testNaskPrepaidFunds()
    {
        //ObjectSpec::overwriteParent();
        $frame = new Report();
        $frame->setFundsAccountType('DOMAIN');
        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8"?>
<epp xmlns="http://www.dns.pl/nask-epp-schema/epp-2.0">
<extension>
<extreport:report
xmlns:extreport="http://www.dns.pl/nask-epp-schema/extreport-2.0">
<extreport:prepaid>
<extreport:paymentFunds>
<extreport:accountType>DOMAIN</extreport:accountType>
</extreport:paymentFunds>
</extreport:prepaid>
</extreport:report>
</extension>
</epp>
            ',
            (string) $frame
            );
    }
}

