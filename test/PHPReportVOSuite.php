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
require_once PHPREPORT_ROOT . '/test/VOTests/UserVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/AreaVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/UserGroupVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/ExtraHourVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/TaskVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/CustomerVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/SectorVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/CustomEventVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/ProjectScheduleVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/CityVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/CommonEventVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/JourneyHistoryVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/HourCostHistoryVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/AreaHistoryVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/CityHistoryVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/ProjectVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/IterationVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/ModuleVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/StoryVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/SectionVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/TaskStoryVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/TaskSectionVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/CustomTaskSectionVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/CustomSectionVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/CustomTaskStoryVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/CustomStoryVOTests.php';
require_once PHPREPORT_ROOT . '/test/VOTests/CustomProjectVOTests.php';

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
    $suite->addTestSuite('IterationVOTests');
    $suite->addTestSuite('ModuleVOTests');
    $suite->addTestSuite('StoryVOTests');
    $suite->addTestSuite('SectionVOTests');
    $suite->addTestSuite('TaskSectionVOTests');
    $suite->addTestSuite('TaskStoryVOTests');
    $suite->addTestSuite('CustomTaskSectionVOTests');
    $suite->addTestSuite('CustomTaskStoryVOTests');
    $suite->addTestSuite('CustomSectionVOTests');
    $suite->addTestSuite('CustomStoryVOTests');
    $suite->addTestSuite('CustomProjectVOTests');

        return $suite;
    }

}
?>
