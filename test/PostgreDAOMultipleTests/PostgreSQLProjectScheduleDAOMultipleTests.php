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
include_once(PHPREPORT_ROOT . '/model/dao/ProjectScheduleDAO/PostgreSQLProjectScheduleDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ProjectDAO/PostgreSQLProjectDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/PostgreSQLUserDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/AreaDAO/PostgreSQLAreaDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/WorksDAO/PostgreSQLWorksDAO.php');

class PostgreSQLProjectScheduleDAOMultipleTests extends PHPUnit_Framework_TestCase
{

    protected $dao;
    protected $dao2;
    protected $testObjects;
    protected $auxDao;
    protected $auxObjects;
    protected $auxDao2;
    protected $auxObjects2;
    protected $auxDao3;
    protected $auxObject;

    protected function setUp()
    {

        $this->auxDao = new PostgreSQLUserDAO();

        $this->auxObjects[0] = new UserVO();
        $this->auxObjects[0]->setLogin("bender");
        $this->auxObjects[0]->setPassword("kiss my metal shiny ass");
        $this->auxObjects[0]->setId(-1);

        $this->auxObjects[1] = new UserVO();
        $this->auxObjects[1]->setLogin("flexo");
        $this->auxObjects[1]->setPassword("kiss my metal shiny ass");
        $this->auxObjects[1]->setId(-1);

        $this->auxObjects[2] = new UserVO();
        $this->auxObjects[2]->setLogin("roberto");
        $this->auxObjects[2]->setPassword("kiss my metal shiny ass");
        $this->auxObjects[2]->setId(-1);

        $this->auxDao->create($this->auxObjects[0]);

        $this->auxDao3 = new PostgreSQLAreaDAO();

        $this->auxObject = new AreaVO();
        $this->auxObject->setName("Deliverers");

        $this->auxDao3->create($this->auxObject);

        $this->auxDao2 = new PostgreSQLProjectDAO();

        $this->auxObjects2[0] = new ProjectVO();
        $this->auxObjects2[0]->setInit(date_create("1999-12-31"));
        $this->auxObjects2[0]->setAreaId($this->auxObject->getId());
        $this->auxObjects2[0]->setEnd(date_create("2999-12-31"));
        $this->auxObjects2[0]->setDescription("Good news, everyone!");
        $this->auxObjects2[0]->setActivation(TRUE);
        $this->auxObjects2[0]->setSchedType("Good news, everyone!");
        $this->auxObjects2[0]->setType("I've taught the toaster to feel love!");
        $this->auxObjects2[0]->setMovedHours(3.14);
        $this->auxObjects2[0]->setInvoice(5.55);
        $this->auxObjects2[0]->setEstHours(3.25);
        $this->auxObjects2[0]->setId(-1);

        $this->auxObjects2[1] = new ProjectVO();
        $this->auxObjects2[1]->setInit(date_create("2000-12-31"));
        $this->auxObjects2[1]->setAreaId($this->auxObject->getId());
        $this->auxObjects2[1]->setEnd(date_create("2999-12-31"));
        $this->auxObjects2[1]->setDescription("Good news, everyone!");
        $this->auxObjects2[1]->setActivation(TRUE);
        $this->auxObjects2[1]->setSchedType("Good news, everyone!");
        $this->auxObjects2[1]->setType("I've taught the toaster to feel love!");
        $this->auxObjects2[1]->setMovedHours(3.14);
        $this->auxObjects2[1]->setInvoice(5.55);
        $this->auxObjects2[1]->setEstHours(3.25);
        $this->auxObjects2[1]->setId(-1);

        $this->auxObjects2[2] = new ProjectVO();
        $this->auxObjects2[2]->setInit(date_create("2001-12-31"));
        $this->auxObjects2[2]->setAreaId($this->auxObject->getId());
        $this->auxObjects2[2]->setEnd(date_create("2999-12-31"));
        $this->auxObjects2[2]->setDescription("Good news, everyone!");
        $this->auxObjects2[2]->setActivation(TRUE);
        $this->auxObjects2[2]->setSchedType("Good news, everyone!");
        $this->auxObjects2[2]->setType("I've taught the toaster to feel love!");
        $this->auxObjects2[2]->setMovedHours(3.14);
        $this->auxObjects2[2]->setInvoice(5.55);
        $this->auxObjects2[2]->setEstHours(3.25);
        $this->auxObjects2[2]->setId(-1);

        $this->auxDao2->create($this->auxObjects2[0]);

        $this->testObjects[0] = new ProjectScheduleVO();

        $this->testObjects[0]->setWeeklyLoad(25.5);
        $this->testObjects[0]->setInitWeek(12);
        $this->testObjects[0]->setInitYear(2005);
        $this->testObjects[0]->setEndWeek(9);
        $this->testObjects[0]->setEndYear(2006);
        $this->testObjects[0]->setUserId($this->auxObjects[0]->getId());
        $this->testObjects[0]->setProjectId($this->auxObjects2[0]->getId());
        $this->testObjects[0]->setId(-1);

        $this->dao = new PostgreSQLProjectScheduleDAO();

        $this->dao2 = new PostgreSQLWorksDAO();

        $this->dao2->create($this->testObjects[0]->getUserId(), $this->testObjects[0]->getProjectId());

    }

    protected function tearDown()
    {

        foreach($this->testObjects as $testObject)
            $this->dao->delete($testObject);

        foreach($this->testObjects as $testObject)
            $this->dao2->delete($testObject->getUserId(), $testObject->getProjectId());

        foreach($this->auxObjects as $testObject)
            $this->auxDao->delete($testObject);

        foreach($this->auxObjects2 as $testObject)
            $this->auxDao2->delete($testObject);

        $this->auxDao3->delete($this->auxObject);

    }

    public function testCreate()
    {

        $this->assertEquals($this->dao->create($this->testObjects[0]), 1);

    }

    public function testDelete()
    {

        $this->dao->create($this->testObjects[0]);

        $this->assertEquals($this->dao->delete($this->testObjects[0]), 1);

    }

    public function testDeleteNonExistent()
    {

        $this->assertEquals($this->dao->delete($this->testObjects[0]), 0);

    }

    public function testIdCreate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setInitWeek(13);

        $this->dao->create($this->testObjects[1]);

        $this->assertGreaterThan($this->testObjects[0]->getId(), $this->testObjects[1]->getId());

    }

    public function testGetById()
    {

        $this->dao->create($this->testObjects[0]);

        $read = $this->dao->getById($this->testObjects[0]->getId());

        $this->assertEquals($read, $this->testObjects[0]);

    }

    public function testGetByIdNonExistent()
    {

        $read = $this->dao->getById(0);

        $this->assertNull($read);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testGetByIdInvalid()
    {

        $read = $this->dao->getById("zoidberg");

    }

    public function testGetByIdAfterDelete()
    {

        $this->dao->create($this->testObjects[0]);

        $this->dao->delete($this->testObjects[0]);

        $read = $this->dao->getById($this->testObjects[0]->getId());

        $this->assertEquals($read, NULL);

    }

    public function testUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setWeeklyLoad(90);

        $this->assertEquals($this->dao->update($this->testObjects[0]), 1);

    }

    public function testGetByIdAfterUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setWeeklyLoad(90);

        $this->dao->update($this->testObjects[0]);

        $read = $this->dao->getById($this->testObjects[0]->getId());

        $this->assertEquals($read, $this->testObjects[0]);

    }

    public function testUpdateNonExistent()
    {

        $this->assertEquals($this->dao->update($this->testObjects[0]), 0);

    }

    public function testGetByUserProjectIds()
    {

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setInitWeek(13);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setInitWeek(14);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByUserProjectIds($this->testObjects[0]->getUserId(), $this->testObjects[0]->getProjectId());

        $this->assertEquals($read, $this->testObjects);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testGetByInvalidUserProjectIds()
    {

        $this->dao->getByUserProjectIds("*", "*");

    }

    public function testGetByUserProjectIdsDate()
    {

        $this->dao->create($this->testObjects[0]);

        $read = $this->dao->getByUserProjectIdsDate($this->testObjects[0]->getUserId(), $this->testObjects[0]->getProjectId(), $this->testObjects[0]->getInitWeek(), $this->testObjects[0]->getInitYear());

        $this->assertEquals($read, $this->testObjects[0]);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testGetByInvalidUserProjectIdsDate()
    {

        $this->dao->getByUserProjectIds("*", "*", $this->testObjects[0]->getInitWeek(), $this->testObjects[0]->getInitYear());

    }

}
?>
