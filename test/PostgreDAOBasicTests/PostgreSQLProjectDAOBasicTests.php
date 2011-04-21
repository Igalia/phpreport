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
include_once(PHPREPORT_ROOT . '/model/vo/AreaVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/AreaDAO/PostgreSQLAreaDAO.php');

class PostgreSQLProjectDAOBasicTests extends PHPUnit_Framework_TestCase
{

    protected $dao;
    protected $testObjects;
    protected $auxDao;
    protected $auxObject;

    protected function setUp()
    {

        $this->auxDao = new PostgreSQLAreaDAO();

        $this->auxObject = new AreaVO();
        $this->auxObject->setName("Deliverers");

        $this->auxDao->create($this->auxObject);

        $this->dao = new PostgreSQLProjectDAO();

        $this->testObjects[0] = new ProjectVO();
        $this->testObjects[0]->setInit(date_create("1999-12-31"));
        $this->testObjects[0]->setAreaId($this->auxObject->getId());
        $this->testObjects[0]->setEnd(date_create("2999-12-31"));
        $this->testObjects[0]->setDescription("Good news, everyone!");
        $this->testObjects[0]->setActivation(TRUE);
        $this->testObjects[0]->setSchedType("Good news, everyone!");
        $this->testObjects[0]->setType("I've taught the toaster to feel love!");
        $this->testObjects[0]->setMovedHours(3.14);
        $this->testObjects[0]->setInvoice(5.55);
        $this->testObjects[0]->setEstHours(3.25);
        $this->testObjects[0]->setId(-1);

    }

    protected function tearDown()
    {
        foreach($this->testObjects as $testObject)
            $this->dao->delete($testObject);

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

    public function testGetAll()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->dao->create($this->testObjects[2]);

        $this->assertEquals($this->testObjects, $this->dao->getAll());

    }

    public function testGetByAreaId()
    {

        $this->testObjects[1] = new ProjectVO();
        $this->testObjects[1]->setInit(date_create("1999-12-31"));
        $this->testObjects[1]->setAreaId($this->auxObject->getId());
        $this->testObjects[1]->setEnd(date_create("2999-12-31"));
        $this->testObjects[1]->setDescription("Good news, everyone!");
        $this->testObjects[1]->setActivation(TRUE);
        $this->testObjects[1]->setSchedType("Good news, everyone!");
        $this->testObjects[1]->setType("I've taught the toaster to feel love!");
        $this->testObjects[1]->setMovedHours(3.14);
        $this->testObjects[1]->setInvoice(5.55);
        $this->testObjects[1]->setEstHours(3.25);
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new ProjectVO();
        $this->testObjects[2]->setInit(date_create("1999-12-31"));
        $this->testObjects[2]->setAreaId($this->auxObject->getId());
        $this->testObjects[2]->setEnd(date_create("2999-12-31"));
        $this->testObjects[2]->setDescription("Good news, everyone!");
        $this->testObjects[2]->setActivation(TRUE);
        $this->testObjects[2]->setSchedType("Good news, everyone!");
        $this->testObjects[2]->setType("I've taught the toaster to feel love!");
        $this->testObjects[2]->setMovedHours(3.14);
        $this->testObjects[2]->setInvoice(5.55);
        $this->testObjects[2]->setEstHours(3.25);
        $this->testObjects[2]->setId(-1);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByAreaId($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetByAreaIdFromArea()
    {

        $this->testObjects[1] = new ProjectVO();
        $this->testObjects[1]->setInit(date_create("1999-12-31"));
        $this->testObjects[1]->setAreaId($this->auxObject->getId());
        $this->testObjects[1]->setEnd(date_create("2999-12-31"));
        $this->testObjects[1]->setDescription("Good news, everyone!");
        $this->testObjects[1]->setActivation(TRUE);
        $this->testObjects[1]->setSchedType("Good news, everyone!");
        $this->testObjects[1]->setType("I've taught the toaster to feel love!");
        $this->testObjects[1]->setMovedHours(3.14);
        $this->testObjects[1]->setInvoice(5.55);
        $this->testObjects[1]->setEstHours(3.25);
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new ProjectVO();
        $this->testObjects[2]->setInit(date_create("1999-12-31"));
        $this->testObjects[2]->setAreaId($this->auxObject->getId());
        $this->testObjects[2]->setEnd(date_create("2999-12-31"));
        $this->testObjects[2]->setDescription("Good news, everyone!");
        $this->testObjects[2]->setActivation(TRUE);
        $this->testObjects[2]->setSchedType("Good news, everyone!");
        $this->testObjects[2]->setType("I've taught the toaster to feel love!");
        $this->testObjects[2]->setMovedHours(3.14);
        $this->testObjects[2]->setInvoice(5.55);
        $this->testObjects[2]->setEstHours(3.25);
        $this->testObjects[2]->setId(-1);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->auxDao->getProjects($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

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

        $this->testObjects[0]->setType(NULL);

        $this->assertEquals($this->dao->update($this->testObjects[0]), 1);

    }

    public function testGetByIdAfterUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setType(NULL);

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
