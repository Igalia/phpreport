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


include_once(PHPREPORT_ROOT . '/model/vo/CustomSectionVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

class CustomSectionVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
    {

        $this->VO = new CustomSectionVO();

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

    public function testReviewerField()
    {

        $user = new UserVO();
        $user->setLogin('Hermes');

        $this->VO->setReviewer($user);

        $this->assertEquals($this->VO->getReviewer(), $user);

        $user->setLogin('Leela');

        $this->VO->setReviewer($user);

        $this->assertEquals($this->VO->getReviewer(), $user);

    }

    public function testDevelopersField()
    {

        $user1 = new UserVO();
        $user1->setLogin('Hermes');

        $user2 = new UserVO();
        $user2->setLogin('Scruffy');

        $user3 = new UserVO();
        $user3->setLogin('Leela');

        $users = array($user1, $user2, $user3);

        $this->VO->setDevelopers($users);

        $this->assertEquals($this->VO->getDevelopers(), $users);

        $users = array($user2, $user1);

        $this->VO->setDevelopers($users);

        $this->assertEquals($this->VO->getDevelopers(), $users);

    }

    public function testToDoField()
    {

        $this->VO->setToDo(25.5);

        $this->assertEquals($this->VO->getToDo(), 25.5);

        $this->VO->setToDo(5);

        $this->assertEquals($this->VO->getToDo(), 5);

    }

    public function testOverrunField()
    {

        $this->VO->setOverrun(15.5);

        $this->assertEquals($this->VO->getOverrun(), 15.5);

        $this->VO->setOverrun(2);

        $this->assertEquals($this->VO->getOverrun(), 2);

    }

    public function testDoneField()
    {

        $this->VO->setDone(15.5);

        $this->assertEquals($this->VO->getDone(), 15.5);

        $this->VO->setDone(2);

        $this->assertEquals($this->VO->getDone(), 2);

    }

    public function testSpentField()
    {

        $this->VO->setSpent(15.5);

        $this->assertEquals($this->VO->getSpent(), 15.5);

        $this->VO->setSpent(2);

        $this->assertEquals($this->VO->getSpent(), 2);

    }

    public function testEstHoursField()
    {

        $this->VO->setEstHours(15.5);

        $this->assertEquals($this->VO->getEstHours(), 15.5);

        $this->VO->setEstHours(2);

        $this->assertEquals($this->VO->getEstHours(), 2);

    }

    public function testAcceptedField()
    {

        $this->VO->setAccepted(TRUE);

        $this->assertTrue($this->VO->getAccepted());

        $this->VO->setAccepted(FALSE);

        $this->assertFalse($this->VO->getAccepted());

    }

    public function testModuleIdField()
    {

        $this->VO->setModuleId(2);

        $this->assertEquals($this->VO->getModuleId(), 2);

        $this->VO->setModuleId(45);

        $this->assertEquals($this->VO->getModuleId(), 45);

    }

    public function testTextField()
    {

        $this->VO->setName('Mars');

        $this->assertEquals($this->VO->getName(), 'Mars');

        $this->VO->setName('Omicron Persei');

        $this->assertEquals($this->VO->getName(), 'Omicron Persei');

    }

}
?>
