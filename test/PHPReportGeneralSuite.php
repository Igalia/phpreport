<?php

require_once 'phpreport/test/PHPReportVOSuite.php';
require_once 'phpreport/test/PHPReportPostgreSuite.php';

class PHPReportGeneralSuite extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
    $suite = new PHPReportGeneralSuite();

    $suite->addTestSuite('PHPReportVOSuite');
    $suite->addTestSuite('PHPReportPostgreSuite');

        return $suite;
    }

}
?>
