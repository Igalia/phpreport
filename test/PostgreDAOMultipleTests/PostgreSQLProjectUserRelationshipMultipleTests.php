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
include_once(PHPREPORT_ROOT . '/model/dao/ProjectDAO/PostgreSQLProjectDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/PostgreSQLUserDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/AreaDAO/PostgreSQLAreaDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ProjectUserDAO/PostgreSQLProjectUserDAO.php');

class PostgreSQLProjectUserRelationshipMultipleTests extends PHPUnit_Framework_TestCase
{

    protected $dao;
    protected $auxDao;
    protected $testObjects;
    protected $auxDao2;
    protected $testObjects2;
    protected $auxDao3;
    protected $auxObject;

    protected function setUp()
    {

        $this->auxDao = new PostgreSQLUserDAO();

        $this->testObjects[0] = new UserVO();
        $this->testObjects[0]->setLogin("bender");
        $this->testObjects[0]->setId(-1);

        $this->testObjects[1] = new UserVO();
        $this->testObjects[1]->setLogin("flexo");
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new UserVO();
        $this->testObjects[2]->setLogin("roberto");
        $this->testObjects[2]->setId(-1);

        $this->auxDao->create($this->testObjects[0]);

        $this->auxDao3 = new PostgreSQLAreaDAO();

        $this->auxObject = new AreaVO();
        $this->auxObject->setName("Deliverers");

        $this->auxDao3->create($this->auxObject);

        $this->auxDao2 = new PostgreSQLProjectDAO();

        $this->testObjects2[0] = new ProjectVO();
        $this->testObjects2[0]->setInit(date_create("1999-12-31"));
        $this->testObjects2[0]->setAreaId($this->auxObject->getId());
        $this->testObjects2[0]->setEnd(date_create("2999-12-31"));
        $this->testObjects2[0]->setDescription("Good news, everyone!");
        $this->testObjects2[0]->setActivation(TRUE);
        $this->testObjects2[0]->setSchedType("Good news, everyone!");
        $this->testObjects2[0]->setType("I've taught the toaster to feel love!");
        $this->testObjects2[0]->setMovedHours(3.14);
        $this->testObjects2[0]->setInvoice(5.55);
        $this->testObjects2[0]->setEstHours(3.25);
        $this->testObjects2[0]->setId(-1);

        $this->testObjects2[1] = new ProjectVO();
        $this->testObjects2[1]->setInit(date_create("1999-12-31"));
        $this->testObjects2[1]->setAreaId($this->auxObject->getId());
        $this->testObjects2[1]->setEnd(date_create("2999-12-31"));
        $this->testObjects2[1]->setDescription("Good news, everyone!");
        $this->testObjects2[1]->setActivation(TRUE);
        $this->testObjects2[1]->setSchedType("Good news, everyone!");
        $this->testObjects2[1]->setType("I've taught the toaster to feel love!");
        $this->testObjects2[1]->setMovedHours(3.14);
        $this->testObjects2[1]->setInvoice(5.55);
        $this->testObjects2[1]->setEstHours(3.25);
        $this->testObjects2[1]->setId(-1);

        $this->testObjects2[2] = new ProjectVO();
        $this->testObjects2[2]->setInit(date_create("1999-12-31"));
        $this->testObjects2[2]->setAreaId($this->auxObject->getId());
        $this->testObjects2[2]->setEnd(date_create("2999-12-31"));
        $this->testObjects2[2]->setDescription("Good news, everyone!");
        $this->testObjects2[2]->setActivation(TRUE);
        $this->testObjects2[2]->setSchedType("Good news, everyone!");
        $this->testObjects2[2]->setType("I've taught the toaster to feel love!");
        $this->testObjects2[2]->setMovedHours(3.14);
        $this->testObjects2[2]->setInvoice(5.55);
        $this->testObjects2[2]->setEstHours(3.25);
        $this->testObjects2[2]->setId(-1);

        $this->auxDao2->create($this->testObjects2[0]);

        $this->dao = new PostgreSQLProjectUserDAO();

    }

    protected function tearDown()
    {
        foreach($this->testObjects as $testObject1)
            foreach($this->testObjects2 as $testObject2)
                $this->dao->delete($testObject1->getId(), $testObject2->getId());

        foreach($this->testObjects as $testObject)
            $this->auxDao->delete($testObject);

        foreach($this->testObjects2 as $testObject)
            $this->auxDao2->delete($testObject);

        $this->auxDao3->delete($this->auxObject);

    }

    public function testCreate()
    {

        $this->assertEquals($this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId()), 1);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testCreateId1Invalid()
    {

        $this->dao->create("*", $this->testObjects2[0]->getId());

    }

    /**
         * @expectedException SQLIncorrectTypeException
         */
    public function testCreateId2Invalid()
    {

        $this->dao->create($this->testObjects[0]->getId(), "*");

    }

    public function testDelete()
    {

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());

        $this->assertEquals($this->dao->delete($this->testObjects[0]->getId(), $this->testObjects2[0]->getId()), 1);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testDeleteId1Invalid()
    {

        $this->dao->delete("*", $this->testObjects2[0]->getId());

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testDeleteId2Invalid()
    {

        $this->dao->delete($this->testObjects[0]->getId(), "*");

    }

    public function testDeleteNonExistent()
    {

        $this->assertEquals($this->dao->delete($this->testObjects[0]->getId(), $this->testObjects2[0]->getId()), 0);

    }

    public function testGetByUserId()
    {

        $this->auxDao2->create($this->testObjects2[1]);
        $this->auxDao2->create($this->testObjects2[2]);

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[1]->getId());
        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[2]->getId());

        $read = $this->dao->getByUserId($this->testObjects[0]->getId());

        $this->assertEquals($read, $this->testObjects2);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testGetByInvalidUserId()
    {

        $this->dao->getByUserId("*");

    }

    public function testGetByProjectId()
    {

        $this->auxDao->create($this->testObjects[1]);
        $this->auxDao->create($this->testObjects[2]);

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[1]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[2]->getId(), $this->testObjects2[0]->getId());

        $read = $this->dao->getByProjectId($this->testObjects2[0]->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testGetByInvalidProjectId()
    {

        $this->dao->getByProjectId("*");

    }

}
?>
