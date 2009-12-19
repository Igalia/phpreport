<?php

require_once 'phpreport/test/PHPReportPostgreDAOBasicSuite.php';
require_once 'phpreport/test/PHPReportPostgreDAOMultipleSuite.php';

class PHPReportPostgreSuite extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
    $suite = new PHPReportPostgreSuite();

    $suite->addTestSuite('PHPReportPostgreDAOBasicSuite');
    $suite->addTestSuite('PHPReportPostgreDAOMultipleSuite');

        return $suite;
    }

}
?>
