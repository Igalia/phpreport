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


include_once(PHPREPORT_ROOT . '/model/vo/ProjectScheduleVO.php');

class ProjectScheduleVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
    {

        $this->VO = new ProjectScheduleVO();

    }

    public function testNew()
    {

        $this->assertNotNull($this->VO);

    }

    public function testIdField()
    {

        $this->VO->setId(1);

        $this->assertEquals($this->VO->getId(), 1);

        $this->VO->setId(2);

        $this->assertEquals($this->VO->getId(), 2);

    }

    public function testWeeklyLoadField()
    {

        $this->VO->setWeeklyLoad(2.5);

        $this->assertEquals($this->VO->getWeeklyLoad(), 2.5);

        $this->VO->setWeeklyLoad(4.5);

        $this->assertEquals($this->VO->getWeeklyLoad(), 4.5);

    }

    public function testInitWeekField()
    {

        $this->VO->setInitWeek(1);

        $this->assertEquals($this->VO->getInitWeek(), 1);

        $this->VO->setInitWeek(50);

        $this->assertEquals($this->VO->getInitWeek(), 50);

    }

    public function testEndWeekField()
    {

        $this->VO->setEndWeek(1);

        $this->assertEquals($this->VO->getEndWeek(), 1);

        $this->VO->setEndWeek(50);

        $this->assertEquals($this->VO->getEndWeek(), 50);

    }

    public function testInitYearField()
    {

        $this->VO->setInitYear(2000);

        $this->assertEquals($this->VO->getInitYear(), 2000);

        $this->VO->setInitYear(3000);

        $this->assertEquals($this->VO->getInitYear(), 3000);

    }

    public function testEndYearField()
    {

        $this->VO->setEndYear(2000);

        $this->assertEquals($this->VO->getEndYear(), 2000);

        $this->VO->setEndYear(3000);

        $this->assertEquals($this->VO->getEndYear(), 3000);

    }

    public function testUserIdField()
    {

        $this->VO->setUserId(2);

        $this->assertEquals($this->VO->getUserId(), 2);

        $this->VO->setUserId(45);

        $this->assertEquals($this->VO->getUserId(), 45);

    }

    public function testProjectIdField()
    {

        $this->VO->setProjectId(2);

        $this->assertEquals($this->VO->getProjectId(), 2);

        $this->VO->setProjectId(45);

        $this->assertEquals($this->VO->getProjectId(), 45);

    }

}
?>
