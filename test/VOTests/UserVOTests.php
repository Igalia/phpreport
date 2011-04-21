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


include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

class UserVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
    {

        $this->VO = new UserVO();

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

    public function testLoginField()
    {

        $this->VO->setLogin("bender");

        $this->assertEquals($this->VO->getLogin(), "bender");

        $this->VO->setLogin("fry");

        $this->assertEquals($this->VO->getLogin(), "fry");

    }

    public function testPasswordField()
    {

        $this->VO->setPassword("kiss my shiny metal ass");

        $this->assertEquals($this->VO->getPassword(), "kiss my shiny metal ass");

        $this->VO->setPassword("you meat-bag");

        $this->assertEquals($this->VO->getPassword(), "you meat-bag");

    }

    public function testGroupsField()
    {

        $groups = array("Fry", "Leela", "Bender", "Hubert");

        $this->VO->setGroups($groups);

        $this->assertEquals($this->VO->getGroups(), $groups);

        $groups[3] = "Zoidberg";

        $this->VO->setGroups($groups);

        $this->assertEquals($this->VO->getGroups(), $groups);

    }

}
?>
