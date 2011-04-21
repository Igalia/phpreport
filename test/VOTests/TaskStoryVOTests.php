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


include_once(PHPREPORT_ROOT . '/model/vo/TaskStoryVO.php');

class TaskStoryVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
    {

        $this->VO = new TaskStoryVO();

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

    public function testNameField()
    {

        $this->VO->setName('Mars');

        $this->assertEquals($this->VO->getName(), 'Mars');

        $this->VO->setName('Omicron Persei');

        $this->assertEquals($this->VO->getName(), 'Omicron Persei');

    }

    public function testEstHoursField()
    {

        $this->VO->setEstHours(15.75);

        $this->assertEquals($this->VO->getEstHours(), 15.75);

        $this->VO->setEstHours(7.66);

        $this->assertEquals($this->VO->getEstHours(), 7.66);

    }

    public function testToDoField()
    {

        $this->VO->setToDo(15.75);

        $this->assertEquals($this->VO->getToDo(), 15.75);

        $this->VO->setToDo(7.66);

        $this->assertEquals($this->VO->getToDo(), 7.66);

    }

    public function testInitField()
    {

        $this->VO->setInit(date_create('1999-12-31'));

        $this->assertEquals($this->VO->getInit(), date_create('1999-12-31'));

        $this->VO->setInit(date_create('2999-12-31'));

        $this->assertEquals($this->VO->getInit(), date_create('2999-12-31'));

    }

    public function testEndField()
    {

        $this->VO->setEnd(date_create('1999-12-31'));

        $this->assertEquals($this->VO->getEnd(), date_create('1999-12-31'));

        $this->VO->setEnd(date_create('2999-12-31'));

        $this->assertEquals($this->VO->getEnd(), date_create('2999-12-31'));

    }

    public function testEstEndField()
    {

        $this->VO->setEstEnd(date_create('1999-12-31'));

        $this->assertEquals($this->VO->getEstEnd(), date_create('1999-12-31'));

        $this->VO->setEstEnd(date_create('2999-12-31'));

        $this->assertEquals($this->VO->getEstEnd(), date_create('2999-12-31'));

    }

    public function testRiskField()
    {

        $this->VO->setRisk(1);

        $this->assertEquals($this->VO->getRisk(), 1);

        $this->VO->setRisk(3);

        $this->assertEquals($this->VO->getRisk(), 3);

    }

    public function testStoryIdField()
    {

        $this->VO->setStoryId(2);

        $this->assertEquals($this->VO->getStoryId(), 2);

        $this->VO->setStoryId(45);

        $this->assertEquals($this->VO->getStoryId(), 45);

    }

    public function testTaskSectionIdField()
    {

        $this->VO->setTaskSectionId(2);

        $this->assertEquals($this->VO->getTaskSectionId(), 2);

        $this->VO->setTaskSectionId(45);

        $this->assertEquals($this->VO->getTaskSectionId(), 45);

    }

    public function testUserIdField()
    {

        $this->VO->setUserId(2);

        $this->assertEquals($this->VO->getUserId(), 2);

        $this->VO->setUserId(45);

        $this->assertEquals($this->VO->getUserId(), 45);

    }

}
?>
