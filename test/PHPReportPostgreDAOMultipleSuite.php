<?php

require_once 'phpreport/test/PostgreDAOMultipleTests/PostgreSQLBelongsRelationshipMultipleTests.php';
require_once 'phpreport/test/PostgreDAOMultipleTests/PostgreSQLRequestsRelationshipMultipleTests.php';
require_once 'phpreport/test/PostgreDAOMultipleTests/PostgreSQLWorksRelationshipMultipleTests.php';
require_once 'phpreport/test/PostgreDAOMultipleTests/PostgreSQLProjectUserRelationshipMultipleTests.php';
require_once 'phpreport/test/PostgreDAOMultipleTests/PostgreSQLProjectScheduleDAOMultipleTests.php';

class PHPReportPostgreDAOMultipleSuite extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
    $suite = new PHPReportPostgreDAOMultipleSuite();

    $suite->addTestSuite('PostgreSQLBelongsRelationshipMultipleTests');
    $suite->addTestSuite('PostgreSQLRequestsRelationshipMultipleTests');
    $suite->addTestSuite('PostgreSQLWorksRelationshipMultipleTests');
    $suite->addTestSuite('PostgreSQLProjectUserRelationshipMultipleTests');
    $suite->addTestSuite('PostgreSQLProjectScheduleDAOMultipleTests');

        return $suite;
    }

}
?>
