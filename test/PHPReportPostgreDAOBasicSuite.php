<?php

require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLUserDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLAreaDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLCityDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLSectorDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLUserGroupDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLExtraHourDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLCustomEventDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLCommonEventDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLCustomerDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLTaskDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLJourneyHistoryDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLAreaHistoryDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLCityHistoryDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLHourCostHistoryDAOBasicTests.php';
require_once 'phpreport/test/PostgreDAOBasicTests/PostgreSQLProjectDAOBasicTests.php';

class PHPReportPostgreDAOBasicSuite extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
    $suite = new PHPReportPostgreDAOBasicSuite();

    $suite->addTestSuite('PostgreSQLUserDAOBasicTests');
    $suite->addTestSuite('PostgreSQLAreaDAOBasicTests');
    $suite->addTestSuite('PostgreSQLCityDAOBasicTests');
    $suite->addTestSuite('PostgreSQLSectorDAOBasicTests');
    $suite->addTestSuite('PostgreSQLUserGroupDAOBasicTests');
    $suite->addTestSuite('PostgreSQLExtraHourDAOBasicTests');
    $suite->addTestSuite('PostgreSQLCustomEventDAOBasicTests');
    $suite->addTestSuite('PostgreSQLCommonEventDAOBasicTests');
    $suite->addTestSuite('PostgreSQLCustomerDAOBasicTests');
    $suite->addTestSuite('PostgreSQLTaskDAOBasicTests');
    $suite->addTestSuite('PostgreSQLJourneyHistoryDAOBasicTests');
    $suite->addTestSuite('PostgreSQLAreaHistoryDAOBasicTests');
    $suite->addTestSuite('PostgreSQLCityHistoryDAOBasicTests');
    $suite->addTestSuite('PostgreSQLHourCostHistoryDAOBasicTests');
    $suite->addTestSuite('PostgreSQLProjectDAOBasicTests');

        return $suite;
    }

}
?>
