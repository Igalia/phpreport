<?php

require_once 'phpreport/test/VOTests/UserVOTests.php';
require_once 'phpreport/test/VOTests/AreaVOTests.php';
require_once 'phpreport/test/VOTests/UserGroupVOTests.php';
require_once 'phpreport/test/VOTests/ExtraHourVOTests.php';
require_once 'phpreport/test/VOTests/TaskVOTests.php';
require_once 'phpreport/test/VOTests/CustomerVOTests.php';
require_once 'phpreport/test/VOTests/SectorVOTests.php';
require_once 'phpreport/test/VOTests/CustomEventVOTests.php';
require_once 'phpreport/test/VOTests/ProjectScheduleVOTests.php';
require_once 'phpreport/test/VOTests/CityVOTests.php';
require_once 'phpreport/test/VOTests/CommonEventVOTests.php';
require_once 'phpreport/test/VOTests/JourneyHistoryVOTests.php';
require_once 'phpreport/test/VOTests/HourCostHistoryVOTests.php';
require_once 'phpreport/test/VOTests/AreaHistoryVOTests.php';
require_once 'phpreport/test/VOTests/CityHistoryVOTests.php';
require_once 'phpreport/test/VOTests/ProjectVOTests.php';

class PHPReportVOSuite extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
    $suite = new PHPReportVOSuite();

    $suite->addTestSuite('UserVOTests');
    $suite->addTestSuite('AreaVOTests');
    $suite->addTestSuite('UserGroupVOTests');
    $suite->addTestSuite('ExtraHourVOTests');
    $suite->addTestSuite('TaskVOTests');
    $suite->addTestSuite('CustomerVOTests');
    $suite->addTestSuite('SectorVOTests');
    $suite->addTestSuite('CustomEventVOTests');
    $suite->addTestSuite('ProjectScheduleVOTests');
    $suite->addTestSuite('CityVOTests');
    $suite->addTestSuite('CommonEventVOTests');
    $suite->addTestSuite('JourneyHistoryVOTests');
    $suite->addTestSuite('HourCostHistoryVOTests');
    $suite->addTestSuite('AreaHistoryVOTests');
    $suite->addTestSuite('CityHistoryVOTests');
    $suite->addTestSuite('ProjectVOTests');

        return $suite;
    }

}
?>
