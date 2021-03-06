<?php

namespace AfriCC\Tests\EPP\Extension\NASK\Report;

use AfriCC\EPP\Extension\NASK\Report\Host as Report;
use AfriCC\EPP\Extension\NASK\ObjectSpec;
use PHPUnit\Framework\TestCase;

class ReportHostTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        ObjectSpec::overwriteParent();
    }

    public function tearDown()
    {
        ObjectSpec::restoreParent();
    }

    public function testReportHostFrame()
    {
        $frame = new Report();

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
            <epp xmlns="http://www.dns.pl/nask-epp-schema/epp-2.0">
              <extension>
                <extreport:report xmlns:extreport="http://www.dns.pl/nask-epp-schema/extreport-2.0">
                  <extreport:host />
                </extreport:report>
              </extension>
            </epp>
            ',
            (string) $frame
            );
    }

    public function testReportSpecificHostFrame()
    {
        $frame = new Report();
        $frame->setName('ns1.temp.pl');

        $this->assertXmlStringEqualsXmlString(
            '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
            <epp xmlns="http://www.dns.pl/nask-epp-schema/epp-2.0">
              <extension>
                <extreport:report xmlns:extreport="http://www.dns.pl/nask-epp-schema/extreport-2.0">
                  <extreport:host>
                    <extreport:name>ns1.temp.pl</extreport:name>
                  </extreport:host>
                </extreport:report>
              </extension>
            </epp>
            ',
            (string) $frame
            );
    }
}

