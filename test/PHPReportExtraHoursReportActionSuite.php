<?php

require_once 'phpreport/test/FacadeTests/ExtraHoursReportActionLargeTest.php';
require_once 'phpreport/test/FacadeTests/ExtraHoursReportActionMediumTest.php';
require_once 'phpreport/test/FacadeTests/ExtraHoursReportActionSmallTest.php';
require_once 'phpreport/test/FacadeTests/ExtraHoursReportActionVerySmallTest.php';

class PHPReportExtraHoursReportActionSuite extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
    $suite = new PHPReportExtraHoursReportActionSuite();

    $suite->addTestSuite('ExtraHoursReportActionLargeTest');
    $suite->addTestSuite('ExtraHoursReportActionMediumTest');
    $suite->addTestSuite('ExtraHoursReportActionSmallTest');
    $suite->addTestSuite('ExtraHoursReportActionVerySmallTest');

        return $suite;
    }

}
?>
