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


include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');

class ProjectVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
    {

        $this->VO = new ProjectVO();

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

    public function testInvoiceField()
    {

        $this->VO->setInvoice(2.54);

        $this->assertEquals($this->VO->getInvoice(), 2.54);

        $this->VO->setInvoice(4.54);

        $this->assertEquals($this->VO->getInvoice(), 4.54);

    }

    public function testEstHoursField()
    {

        $this->VO->setEstHours(2.54);

        $this->assertEquals($this->VO->getEstHours(), 2.54);

        $this->VO->setEstHours(4.54);

        $this->assertEquals($this->VO->getEstHours(), 4.54);

    }

    public function testMovedHoursField()
    {

        $this->VO->setMovedHours(2.54);

        $this->assertEquals($this->VO->getMovedHours(), 2.54);

        $this->VO->setMovedHours(4.54);

        $this->assertEquals($this->VO->getMovedHours(), 4.54);

    }

    public function testDescriptionField()
    {

        $this->VO->setDescription("Good news, everyone!");

        $this->assertEquals($this->VO->getDescription(), "Good news, everyone!");

        $this->VO->setDescription("I've taught the toaster to feel love!");

        $this->assertEquals($this->VO->getDescription(), "I've taught the toaster to feel love!");

    }

    public function testActivationField()
    {

        $this->VO->setActivation(TRUE);

        $this->assertEquals($this->VO->getActivation(), TRUE);

        $this->VO->setActivation(FALSE);

        $this->assertEquals($this->VO->getActivation(), FALSE);

    }

    public function testTypeField()
    {

        $this->VO->setType("Good news, everyone!");

        $this->assertEquals($this->VO->getType(), "Good news, everyone!");

        $this->VO->setType("I've taught the toaster to feel love!");

        $this->assertEquals($this->VO->getType(), "I've taught the toaster to feel love!");

    }

    public function testSchedTypeField()
    {

        $this->VO->setSchedType("Good news, everyone!");

        $this->assertEquals($this->VO->getSchedType(), "Good news, everyone!");

        $this->VO->setSchedType("I've taught the toaster to feel love!");

        $this->assertEquals($this->VO->getSchedType(), "I've taught the toaster to feel love!");

    }

    public function testAreaIdField()
    {

        $this->VO->setAreaId(2);

        $this->assertEquals($this->VO->getAreaId(), 2);

        $this->VO->setAreaId(45);

        $this->assertEquals($this->VO->getAreaId(), 45);

    }

}
?>
