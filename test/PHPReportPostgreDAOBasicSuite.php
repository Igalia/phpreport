<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


define('PHPREPORT_ROOT', __DIR__ . '/../');
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLUserDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLAreaDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLCityDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLSectorDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLUserGroupDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLExtraHourDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLCustomEventDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLCommonEventDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLCustomerDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLTaskDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLJourneyHistoryDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLAreaHistoryDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLCityHistoryDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLHourCostHistoryDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLProjectDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLIterationDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLModuleDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLStoryDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLSectionDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLTaskStoryDAOBasicTests.php';
require_once PHPREPORT_ROOT . '/test/PostgreDAOBasicTests/PostgreSQLTaskSectionDAOBasicTests.php';

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
    $suite->addTestSuite('PostgreSQLModuleDAOBasicTests');
    $suite->addTestSuite('PostgreSQLIterationDAOBasicTests');
    $suite->addTestSuite('PostgreSQLSectionDAOBasicTests');
    $suite->addTestSuite('PostgreSQLStoryDAOBasicTests');
    $suite->addTestSuite('PostgreSQLTaskSectionDAOBasicTests');
    $suite->addTestSuite('PostgreSQLTaskStoryDAOBasicTests');

        return $suite;
    }

}
?>
