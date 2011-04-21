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


include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');

class TaskVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
    {

        $this->VO = new TaskVO();

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

    public function testInitField()
    {

        $this->VO->setInit(2);

        $this->assertEquals($this->VO->getInit(), 2);

        $this->VO->setInit(4);

        $this->assertEquals($this->VO->getInit(), 4);

    }

    public function testEndField()
    {

        $this->VO->setEnd(2);

        $this->assertEquals($this->VO->getEnd(), 2);

        $this->VO->setEnd(4);

        $this->assertEquals($this->VO->getEnd(), 4);

    }

    public function testStoryField()
    {

        $this->VO->setStory("Good news, everyone!");

        $this->assertEquals($this->VO->getStory(), "Good news, everyone!");

        $this->VO->setStory("I've taught the toaster to feel love!");

        $this->assertEquals($this->VO->getStory(), "I've taught the toaster to feel love!");

    }

    public function testTeleworkField()
    {

        $this->VO->setTelework(TRUE);

        $this->assertEquals($this->VO->getTelework(), TRUE);

        $this->VO->setTelework(FALSE);

        $this->assertEquals($this->VO->getTelework(), FALSE);

    }

    public function testTextField()
    {

        $this->VO->setText("Good news, everyone!");

        $this->assertEquals($this->VO->getText(), "Good news, everyone!");

        $this->VO->setText("I've taught the toaster to feel love!");

        $this->assertEquals($this->VO->getText(), "I've taught the toaster to feel love!");

    }

    public function testTtypeField()
    {

        $this->VO->setTtype("Good news, everyone!");

        $this->assertEquals($this->VO->getTtype(), "Good news, everyone!");

        $this->VO->setTtype("I've taught the toaster to feel love!");

        $this->assertEquals($this->VO->getTtype(), "I've taught the toaster to feel love!");

    }

    public function testPhaseField()
    {

        $this->VO->setPhase("Scruffy");

        $this->assertEquals($this->VO->getPhase(), "Scruffy");

        $this->VO->setPhase("Nibbles");

        $this->assertEquals($this->VO->getPhase(), "Nibbles");

    }

    public function testUsrIdField()
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

    public function testCustomerIdField()
    {

        $this->VO->setCustomerId(2);

        $this->assertEquals($this->VO->getCustomerId(), 2);

        $this->VO->setCustomerId(45);

        $this->assertEquals($this->VO->getCustomerId(), 45);

    }

}
?>
