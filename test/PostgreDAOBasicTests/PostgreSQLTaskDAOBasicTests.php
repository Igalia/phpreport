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


include_once(PHPREPORT_ROOT . '/util/TaskReportInvalidParameterException.php');
include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskDAO/PostgreSQLTaskDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/PostgreSQLUserDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/SectorVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/SectorDAO/PostgreSQLSectorDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CustomerDAO/PostgreSQLCustomerDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/AreaDAO/PostgreSQLAreaDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ProjectDAO/PostgreSQLProjectDAO.php');

class PostgreSQLTaskDAOBasicTests extends PHPUnit_Framework_TestCase
{

    protected $dao;
    protected $auxObjects;
    protected $auxDao;
    protected $auxObject;

    protected function setUp()
    {

        $this->auxDao = new PostgreSQLUserDAO();

        $this->auxObject = new UserVO();
        $this->auxObject->setLogin("bender");
        $this->auxObject->setPassword("kiss my metal shiny ass");

        $this->auxDao->create($this->auxObject);

        $this->dao = new PostgreSQLTaskDAO();

        $this->testObjects[0] = new TaskVO();
        $this->testObjects[0]->setDate(date_create("1999-12-31"));
        $this->testObjects[0]->setUserId($this->auxObject->getId());
        $this->testObjects[0]->setInit(2);
        $this->testObjects[0]->setEnd(3);
        $this->testObjects[0]->setStory("Good news, everyone!");
        $this->testObjects[0]->setTelework(TRUE);
        $this->testObjects[0]->setText("Good news, everyone!");
        $this->testObjects[0]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[0]->setPhase("Scruffy");
        $this->testObjects[0]->setId(-1);

    }

    protected function tearDown()
    {
        foreach($this->testObjects as $auxObject)
            $this->dao->delete($auxObject);

        $this->auxDao->delete($this->auxObject);

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

    public function testIdCreate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

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

    public function testGetTaskReportByUser()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("2001-11-30"));

        $this->dao->create($this->testObjects[2]);

        $add_hours = (($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit())+($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())+($this->testObjects[2]->getEnd()-$this->testObjects[2]->getInit()))/60.00;

        $res = $this->dao->getTaskReport($this->auxObject);

        $this->assertEquals($add_hours, $res[0][add_hours]);

    }

    public function testGetTaskReportByUserDates()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("1999-11-30"));

        $this->dao->create($this->testObjects[2]);

        $add_hours = (($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())+($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit()))/60.00;

        $res = $this->dao->getTaskReport($this->auxObject, date_create("1999-12-31"), date_create("2999-11-30"));

        $this->assertEquals($add_hours, $res[0][add_hours]);

    }

    public function testGetTaskReportByUserDatesGroupByType()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("2001-11-30"));

        $this->testObjects[2]->setTtype("My name's Scruffy");

        $this->dao->create($this->testObjects[2]);

        $add_hours1 = (($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())+($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit()))/60.00;

        $add_hours2 = ($this->testObjects[2]->getEnd()-$this->testObjects[2]->getInit())/60.00;

        $res = $this->dao->getTaskReport($this->auxObject, date_create("1999-12-31"), date_create("2999-11-30"), "TTYPE");

        $this->assertEquals($add_hours1, $res[0][add_hours]);
        $this->assertEquals($add_hours2, $res[1][add_hours]);

    }

    public function testGetTaskReportByCustomer()
    {

        $dao2 = new PostgreSQLSectorDAO();

        $auxObject2 = new SectorVO();
        $auxObject2->setName("Industry");

        $dao2->create($auxObject2);

        $dao = new PostgreSQLCustomerDAO();

        $auxObject = new CustomerVO();
        $auxObject->setSectorId($auxObject2->getId());
        $auxObject->setName("Mommy");
        $auxObject->setType("Biggest industry on Earth");
        $auxObject->setURL("www.mommyindustries.com");
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[0]->setCustomerId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("2001-11-30"));

        $this->dao->create($this->testObjects[2]);

        $add_hours = (($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit())+($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())+($this->testObjects[2]->getEnd()-$this->testObjects[2]->getInit()))/60.00;

        $res = $this->dao->getTaskReport($auxObject);

        $this->assertEquals($add_hours, $res[0][add_hours]);

        foreach($this->testObjects as $aux)
            $this->dao->delete($aux);

        $dao->delete($auxObject);

        $dao2->delete($auxObject2);

    }

    public function testGetTaskReportByCustomerDates()
    {

        $dao2 = new PostgreSQLSectorDAO();

        $auxObject2 = new SectorVO();
        $auxObject2->setName("Industry");

        $dao2->create($auxObject2);

        $dao = new PostgreSQLCustomerDAO();

        $auxObject = new CustomerVO();
        $auxObject->setSectorId($auxObject2->getId());
        $auxObject->setName("Mommy");
        $auxObject->setType("Biggest industry on Earth");
        $auxObject->setURL("www.mommyindustries.com");
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[0]->setCustomerId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("1999-11-30"));

        $this->dao->create($this->testObjects[2]);

        $add_hours = (($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())+($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit()))/60.00;

        $res = $this->dao->getTaskReport($auxObject, date_create("1999-12-31"), date_create("2999-11-30"));

        $this->assertEquals($add_hours, $res[0][add_hours]);

        foreach($this->testObjects as $aux)
            $this->dao->delete($aux);

        $dao->delete($auxObject);

        $dao2->delete($auxObject2);

    }

    public function testGetTaskReportByCustomerDatesGroupByType()
    {

        $dao2 = new PostgreSQLSectorDAO();

        $auxObject2 = new SectorVO();
        $auxObject2->setName("Industry");

        $dao2->create($auxObject2);

        $dao = new PostgreSQLCustomerDAO();

        $auxObject = new CustomerVO();
        $auxObject->setSectorId($auxObject2->getId());
        $auxObject->setName("Mommy");
        $auxObject->setType("Biggest industry on Earth");
        $auxObject->setURL("www.mommyindustries.com");
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[0]->setCustomerId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("2001-11-30"));

        $this->testObjects[2]->setTtype("My name's Scruffy");

        $this->dao->create($this->testObjects[2]);

        $add_hours1 = (($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())+($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit()))/60.00;

        $add_hours2 = ($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())/60.00;

        $res = $this->dao->getTaskReport($this->auxObject, date_create("1999-12-31"), date_create("2999-11-30"), "TTYPE");

        $this->assertEquals($add_hours1, $res[0][add_hours]);
        $this->assertEquals($add_hours2, $res[1][add_hours]);

        foreach($this->testObjects as $aux)
            $this->dao->delete($aux);

        $dao->delete($auxObject);

        $dao2->delete($auxObject2);

    }

    public function testGetTaskReportByProject()
    {

        $dao2 = new PostgreSQLAreaDAO();

        $auxObject2 = new AreaVO();
        $auxObject2->setName("Deliverers");

        $dao2->create($auxObject2);

        $dao = new PostgreSQLProjectDAO();

        $auxObject = new ProjectVO();
        $auxObject->setInit(date_create("1999-12-31"));
        $auxObject->setAreaId($auxObject2->getId());
        $auxObject->setEnd(date_create("2999-12-31"));
        $auxObject->setDescription("Good news, everyone!");
        $auxObject->setActivation(TRUE);
        $auxObject->setSchedType("Good news, everyone!");
        $auxObject->setType("I've taught the toaster to feel love!");
        $auxObject->setMovedHours(3.14);
        $auxObject->setInvoice(5.55);
        $auxObject->setEstHours(3.25);
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[0]->setProjectId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("2001-11-30"));

        $this->dao->create($this->testObjects[2]);

        $add_hours = (($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit())+($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())+($this->testObjects[2]->getEnd()-$this->testObjects[2]->getInit()))/60.00;

        $res = $this->dao->getTaskReport($auxObject);

        $this->assertEquals($add_hours, $res[0][add_hours]);

        foreach($this->testObjects as $aux)
            $this->dao->delete($aux);

        $dao->delete($auxObject);

        $dao2->delete($auxObject2);

    }

    public function testGetTaskReportByProjectDates()
    {

        $dao2 = new PostgreSQLAreaDAO();

        $auxObject2 = new AreaVO();
        $auxObject2->setName("Deliverers");

        $dao2->create($auxObject2);

        $dao = new PostgreSQLProjectDAO();

        $auxObject = new ProjectVO();
        $auxObject->setInit(date_create("1999-12-31"));
        $auxObject->setAreaId($auxObject2->getId());
        $auxObject->setEnd(date_create("2999-12-31"));
        $auxObject->setDescription("Good news, everyone!");
        $auxObject->setActivation(TRUE);
        $auxObject->setSchedType("Good news, everyone!");
        $auxObject->setType("I've taught the toaster to feel love!");
        $auxObject->setMovedHours(3.14);
        $auxObject->setInvoice(5.55);
        $auxObject->setEstHours(3.25);
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[0]->setProjectId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("3999-11-30"));

        $this->dao->create($this->testObjects[2]);

        $add_hours = (($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())+($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit()))/60.00;

        $res = $this->dao->getTaskReport($auxObject, date_create("1999-12-31"), date_create("2999-11-30"));

        $this->assertEquals($add_hours, $res[0][add_hours]);

        foreach($this->testObjects as $aux)
            $this->dao->delete($aux);

        $dao->delete($auxObject);

        $dao2->delete($auxObject2);

    }

    public function testGetTaskReportByProjectDatesGroupByType()
    {

        $dao2 = new PostgreSQLAreaDAO();

        $auxObject2 = new AreaVO();
        $auxObject2->setName("Deliverers");

        $dao2->create($auxObject2);

        $dao = new PostgreSQLProjectDAO();

        $auxObject = new ProjectVO();
        $auxObject->setInit(date_create("1999-12-31"));
        $auxObject->setAreaId($auxObject2->getId());
        $auxObject->setEnd(date_create("2999-12-31"));
        $auxObject->setDescription("Good news, everyone!");
        $auxObject->setActivation(TRUE);
        $auxObject->setSchedType("Good news, everyone!");
        $auxObject->setType("I've taught the toaster to feel love!");
        $auxObject->setMovedHours(3.14);
        $auxObject->setInvoice(5.55);
        $auxObject->setEstHours(3.25);
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[0]->setProjectId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("2001-11-30"));

        $this->testObjects[2]->setTtype("My name's Scruffy");

        $this->dao->create($this->testObjects[2]);

        $add_hours1 = (($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())+($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit()))/60.00;

        $add_hours2 = ($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())/60.00;

        $res = $this->dao->getTaskReport($this->auxObject, date_create("1999-12-31"), date_create("2999-11-30"), "TTYPE");

        $this->assertEquals($add_hours1, $res[0][add_hours]);
        $this->assertEquals($add_hours2, $res[1][add_hours]);

        foreach($this->testObjects as $aux)
            $this->dao->delete($aux);

        $dao->delete($auxObject);

        $dao2->delete($auxObject2);

    }

    public function testGetGlobalTaskReportByProjectDates()
    {
        $dao3 = new PostgreSQLUserDAO();

        $auxObject3 = new UserVO();
        $auxObject3->setLogin("fry");
        $auxObject3->setPassword("Pizza delivery for I. C. Weiner");

        $dao3->create($auxObject3);

        $dao2 = new PostgreSQLAreaDAO();

        $auxObject2 = new AreaVO();
        $auxObject2->setName("Deliverers");

        $dao2->create($auxObject2);

        $dao = new PostgreSQLProjectDAO();

        $auxObject = new ProjectVO();
        $auxObject->setInit(date_create("1999-12-31"));
        $auxObject->setAreaId($auxObject2->getId());
        $auxObject->setEnd(date_create("2999-12-31"));
        $auxObject->setDescription("Good news, everyone!");
        $auxObject->setActivation(TRUE);
        $auxObject->setSchedType("Good news, everyone!");
        $auxObject->setType("I've taught the toaster to feel love!");
        $auxObject->setMovedHours(3.14);
        $auxObject->setInvoice(5.55);
        $auxObject->setEstHours(3.25);
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[0]->setProjectId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->testObjects[1]->setUserId($auxObject3->getId());

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("3999-11-30"));

        $this->dao->create($this->testObjects[2]);

        $add_hours1 = ($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit())/60.00;
        $add_hours2 = ($this->testObjects[1]->getEnd()-$this->testObjects[1]->getInit())/60.00;

        $res = $this->dao->getGlobalTaskReport(date_create("1999-12-31"), date_create("2999-11-30"), 'USER');

        $this->assertEquals($add_hours1, $res[0][add_hours]);
        $this->assertEquals($add_hours2, $res[1][add_hours]);

        foreach($this->testObjects as $aux)
            $this->dao->delete($aux);

        $dao->delete($auxObject);

        $dao2->delete($auxObject2);

        $dao3->delete($auxObject3);

    }

    public function testGetVacations()
    {

        $dao2 = new PostgreSQLAreaDAO();

        $auxObject2 = new AreaVO();
        $auxObject2->setName("Deliverers");

        $dao2->create($auxObject2);

        $dao = new PostgreSQLProjectDAO();

        $auxObject = new ProjectVO();
        $auxObject->setInit(date_create("1999-12-31"));
        $auxObject->setAreaId($auxObject2->getId());
        $auxObject->setEnd(date_create("2999-12-31"));
        $auxObject->setDescription("vac");
        $auxObject->setActivation(TRUE);
        $auxObject->setSchedType("Good news, everyone!");
        $auxObject->setType("I've taught the toaster to feel love!");
        $auxObject->setMovedHours(3.14);
        $auxObject->setInvoice(5.55);
        $auxObject->setEstHours(3.25);
        $auxObject->setId(-1);

        $auxObject3 = new ProjectVO();
        $auxObject3->setInit(date_create("1999-12-31"));
        $auxObject3->setAreaId($auxObject2->getId());
        $auxObject3->setEnd(date_create("2999-12-31"));
        $auxObject3->setDescription("Good news, everyone!");
        $auxObject3->setActivation(TRUE);
        $auxObject3->setSchedType("Good news, everyone!");
        $auxObject3->setType("I've taught the toaster to feel love!");
        $auxObject3->setMovedHours(3.14);
        $auxObject3->setInvoice(5.55);
        $auxObject3->setEstHours(3.25);
        $auxObject3->setId(-1);

        $dao->create($auxObject);

        $dao->create($auxObject3);

        $this->testObjects[0]->setProjectId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setProjectId($auxObject3->getId());

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("3999-11-30"));

        $this->dao->create($this->testObjects[2]);

        $add_hours = ($this->testObjects[0]->getEnd()-$this->testObjects[0]->getInit())/60.00;

        $res = $this->dao->getVacations($this->auxObject, date_create("1999-12-31"), date_create("2999-11-30"));

        $this->assertEquals($add_hours, $res[add_hours]);

        foreach($this->testObjects as $aux)
            $this->dao->delete($aux);

        $dao->delete($auxObject);

        $dao->delete($auxObject3);

        $dao2->delete($auxObject2);


    }


    /**
      * @expectedException TaskReportInvalidParameterException
      */
    public function testGetTaskInvalidField1()
    {

        $res = $this->dao->getTaskReport($this->auxObject, date_create("1999-12-31"), date_create("2999-11-30"), "ZOIDBERG");

    }


    /**
      * @expectedException TaskReportInvalidParameterException
      */
    public function testGetTaskInvalidField2()
    {

        $res = $this->dao->getTaskReport($this->auxObject, date_create("1999-12-31"), date_create("2999-11-30"), "TTYPE", "ZOIDBERG");

    }

    public function testGetAll()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setDate(date_create("2000-11-30"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setDate(date_create("2001-11-30"));

        $this->dao->create($this->testObjects[2]);

        $this->assertEquals($this->testObjects, $this->dao->getAll());

    }

    public function testGetByUserId()
    {

        $this->testObjects[1] = new TaskVO();
        $this->testObjects[1]->setDate(date_create("2000-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setInit(2);
        $this->testObjects[1]->setEnd(3);
        $this->testObjects[1]->setStory("Good news, everyone!");
        $this->testObjects[1]->setTelework(TRUE);
        $this->testObjects[1]->setText("Good news, everyone!");
        $this->testObjects[1]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[1]->setPhase("Scruffy");
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new TaskVO();
        $this->testObjects[2]->setDate(date_create("2001-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setInit(2);
        $this->testObjects[2]->setEnd(3);
        $this->testObjects[2]->setStory("Good news, everyone!");
        $this->testObjects[2]->setTelework(TRUE);
        $this->testObjects[2]->setText("Good news, everyone!");
        $this->testObjects[2]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[2]->setPhase("Scruffy");
        $this->testObjects[2]->setId(-1);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByUserId($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetByCustomerId()
    {

        $auxDao = new PostgreSQLSectorDAO();

        $auxObject2 = new SectorVO();
        $auxObject2->setName("Industry");

        $auxDao->create($auxObject2);

        $dao = new PostgreSQLCustomerDAO();

        $auxObject = new CustomerVO();
        $auxObject->setSectorId($auxObject2->getId());
        $auxObject->setName("Mommy");
        $auxObject->setType("Biggest industry on Earth");
        $auxObject->setURL("www.mommyindustries.com");
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[1] = new TaskVO();
        $this->testObjects[1]->setDate(date_create("2000-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setInit(2);
        $this->testObjects[1]->setEnd(3);
        $this->testObjects[1]->setStory("Good news, everyone!");
        $this->testObjects[1]->setTelework(TRUE);
        $this->testObjects[1]->setText("Good news, everyone!");
        $this->testObjects[1]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[1]->setPhase("Scruffy");
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new TaskVO();
        $this->testObjects[2]->setDate(date_create("2001-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setInit(2);
        $this->testObjects[2]->setEnd(3);
        $this->testObjects[2]->setStory("Good news, everyone!");
        $this->testObjects[2]->setTelework(TRUE);
        $this->testObjects[2]->setText("Good news, everyone!");
        $this->testObjects[2]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[2]->setPhase("Scruffy");
        $this->testObjects[2]->setId(-1);

        $this->testObjects[0]->setCustomerId($auxObject->getId());
        $this->testObjects[1]->setCustomerId($auxObject->getId());
        $this->testObjects[2]->setCustomerId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByCustomerId($auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

        $this->dao->delete($this->testObjects[0]);
        $this->dao->delete($this->testObjects[1]);
        $this->dao->delete($this->testObjects[2]);

        $dao->delete($auxObject);

        $auxDao->delete($auxObject2);

    }

    public function testGetByProjectId()
    {

        $auxDao = new PostgreSQLAreaDAO();

        $auxObject2 = new AreaVO();
        $auxObject2->setName("Deliverers");

        $auxDao->create($auxObject2);

        $dao = new PostgreSQLProjectDAO();

        $auxObject = new ProjectVO();
        $auxObject->setInit(date_create("1999-12-31"));
        $auxObject->setAreaId($auxObject2->getId());
        $auxObject->setEnd(date_create("2999-12-31"));
        $auxObject->setDescription("Good news, everyone!");
        $auxObject->setActivation(TRUE);
        $auxObject->setSchedType("Good news, everyone!");
        $auxObject->setType("I've taught the toaster to feel love!");
        $auxObject->setMovedHours(3.14);
        $auxObject->setInvoice(5.55);
        $auxObject->setEstHours(3.25);
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[1] = new TaskVO();
        $this->testObjects[1]->setDate(date_create("2000-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setInit(2);
        $this->testObjects[1]->setEnd(3);
        $this->testObjects[1]->setStory("Good news, everyone!");
        $this->testObjects[1]->setTelework(TRUE);
        $this->testObjects[1]->setText("Good news, everyone!");
        $this->testObjects[1]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[1]->setPhase("Scruffy");
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new TaskVO();
        $this->testObjects[2]->setDate(date_create("2001-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setInit(2);
        $this->testObjects[2]->setEnd(3);
        $this->testObjects[2]->setStory("Good news, everyone!");
        $this->testObjects[2]->setTelework(TRUE);
        $this->testObjects[2]->setText("Good news, everyone!");
        $this->testObjects[2]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[2]->setPhase("Scruffy");
        $this->testObjects[2]->setId(-1);

        $this->testObjects[0]->setProjectId($auxObject->getId());
        $this->testObjects[1]->setProjectId($auxObject->getId());
        $this->testObjects[2]->setProjectId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByProjectId($auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

        $this->dao->delete($this->testObjects[0]);
        $this->dao->delete($this->testObjects[1]);
        $this->dao->delete($this->testObjects[2]);

        $dao->delete($auxObject);

        $auxDao->delete($auxObject2);

    }

    public function testGetByUserIdFromUser()
    {

        $this->testObjects[1] = new TaskVO();
        $this->testObjects[1]->setDate(date_create("2000-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setInit(2);
        $this->testObjects[1]->setEnd(3);
        $this->testObjects[1]->setStory("Good news, everyone!");
        $this->testObjects[1]->setTelework(TRUE);
        $this->testObjects[1]->setText("Good news, everyone!");
        $this->testObjects[1]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[1]->setPhase("Scruffy");
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new TaskVO();
        $this->testObjects[2]->setDate(date_create("2001-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setInit(2);
        $this->testObjects[2]->setEnd(3);
        $this->testObjects[2]->setStory("Good news, everyone!");
        $this->testObjects[2]->setTelework(TRUE);
        $this->testObjects[2]->setText("Good news, everyone!");
        $this->testObjects[2]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[2]->setPhase("Scruffy");
        $this->testObjects[2]->setId(-1);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->auxDao->getTasks($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetByCustomerIdFromCustomer()
    {

        $auxDao = new PostgreSQLSectorDAO();

        $auxObject2 = new SectorVO();
        $auxObject2->setName("Industry");

        $auxDao->create($auxObject2);

        $dao = new PostgreSQLCustomerDAO();

        $auxObject = new CustomerVO();
        $auxObject->setSectorId($auxObject2->getId());
        $auxObject->setName("Mommy");
        $auxObject->setType("Biggest industry on Earth");
        $auxObject->setURL("www.mommyindustries.com");
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[1] = new TaskVO();
        $this->testObjects[1]->setDate(date_create("2000-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setInit(2);
        $this->testObjects[1]->setEnd(3);
        $this->testObjects[1]->setStory("Good news, everyone!");
        $this->testObjects[1]->setTelework(TRUE);
        $this->testObjects[1]->setText("Good news, everyone!");
        $this->testObjects[1]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[1]->setPhase("Scruffy");
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new TaskVO();
        $this->testObjects[2]->setDate(date_create("2001-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setInit(2);
        $this->testObjects[2]->setEnd(3);
        $this->testObjects[2]->setStory("Good news, everyone!");
        $this->testObjects[2]->setTelework(TRUE);
        $this->testObjects[2]->setText("Good news, everyone!");
        $this->testObjects[2]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[2]->setPhase("Scruffy");
        $this->testObjects[2]->setId(-1);

        $this->testObjects[0]->setCustomerId($auxObject->getId());
        $this->testObjects[1]->setCustomerId($auxObject->getId());
        $this->testObjects[2]->setCustomerId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $dao->getTasks($auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

        $this->dao->delete($this->testObjects[0]);
        $this->dao->delete($this->testObjects[1]);
        $this->dao->delete($this->testObjects[2]);

        $dao->delete($auxObject);

        $auxDao->delete($auxObject2);

    }

    public function testGetByProjectIdFromProject()
    {

        $auxDao = new PostgreSQLAreaDAO();

        $auxObject2 = new AreaVO();
        $auxObject2->setName("Deliverers");

        $auxDao->create($auxObject2);

        $dao = new PostgreSQLProjectDAO();

        $auxObject = new ProjectVO();
        $auxObject->setInit(date_create("1999-12-31"));
        $auxObject->setAreaId($auxObject2->getId());
        $auxObject->setEnd(date_create("2999-12-31"));
        $auxObject->setDescription("Good news, everyone!");
        $auxObject->setActivation(TRUE);
        $auxObject->setSchedType("Good news, everyone!");
        $auxObject->setType("I've taught the toaster to feel love!");
        $auxObject->setMovedHours(3.14);
        $auxObject->setInvoice(5.55);
        $auxObject->setEstHours(3.25);
        $auxObject->setId(-1);

        $dao->create($auxObject);

        $this->testObjects[1] = new TaskVO();
        $this->testObjects[1]->setDate(date_create("2000-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setInit(2);
        $this->testObjects[1]->setEnd(3);
        $this->testObjects[1]->setStory("Good news, everyone!");
        $this->testObjects[1]->setTelework(TRUE);
        $this->testObjects[1]->setText("Good news, everyone!");
        $this->testObjects[1]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[1]->setPhase("Scruffy");
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new TaskVO();
        $this->testObjects[2]->setDate(date_create("2001-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setInit(2);
        $this->testObjects[2]->setEnd(3);
        $this->testObjects[2]->setStory("Good news, everyone!");
        $this->testObjects[2]->setTelework(TRUE);
        $this->testObjects[2]->setText("Good news, everyone!");
        $this->testObjects[2]->setTtype("I've taught the toaster to feel love!");
        $this->testObjects[2]->setPhase("Scruffy");
        $this->testObjects[2]->setId(-1);

        $this->testObjects[0]->setProjectId($auxObject->getId());
        $this->testObjects[1]->setProjectId($auxObject->getId());
        $this->testObjects[2]->setProjectId($auxObject->getId());

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $dao->getTasks($auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

        $this->dao->delete($this->testObjects[0]);
        $this->dao->delete($this->testObjects[1]);
        $this->dao->delete($this->testObjects[2]);

        $dao->delete($auxObject);

        $auxDao->delete($auxObject2);

    }

    public function testDeleteNonExistent()
    {

        $this->assertEquals($this->dao->delete($this->testObjects[0]), 0);

    }

    public function testGetByIdAfterDelete()
    {

        $this->dao->create($this->testObjects[0]);

        $this->dao->delete($this->testObjects[0]);

        $read = $this->dao->getById($this->testObjects[0]->getId());

        $this->assertNull($read);

    }

    public function testUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setPhase(NULL);

        $this->assertEquals($this->dao->update($this->testObjects[0]), 1);

    }

    public function testGetByIdAfterUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setPhase(NULL);

        $this->dao->update($this->testObjects[0]);

        $read = $this->dao->getById($this->testObjects[0]->getId());

        $this->assertEquals($read, $this->testObjects[0]);

    }

    public function testUpdateNonExistent()
    {

        $this->assertEquals($this->dao->update($this->testObjects[0]), 0);

    }

}
?>
