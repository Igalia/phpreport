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


include_once(PHPREPORT_ROOT . '/model/vo/ExtraHourVO.php');

class ExtraHourVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
    {

        $this->VO = new ExtraHourVO();

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

    public function testHoursField()
    {

        $this->VO->setHours(2.5);

        $this->assertEquals($this->VO->getHours(), 2.5);

        $this->VO->setHours(4.5);

        $this->assertEquals($this->VO->getHours(), 4.5);

    }

    public function testDateField()
    {

        $this->VO->setDate(date_create('1999-12-31'));

        $this->assertEquals($this->VO->getDate(), date_create('1999-12-31'));

        $this->VO->setDate(date_create('2999-12-31'));

        $this->assertEquals($this->VO->getDate(), date_create('2999-12-31'));

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
