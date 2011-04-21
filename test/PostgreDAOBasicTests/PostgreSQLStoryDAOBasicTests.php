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


include_once(PHPREPORT_ROOT . '/model/vo/StoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/StoryDAO/PostgreSQLStoryDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ProjectDAO/PostgreSQLProjectDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/AreaDAO/PostgreSQLAreaDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/IterationVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/IterationDAO/PostgreSQLIterationDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/PostgreSQLUserDAO.php');

class PostgreSQLStoryDAOBasicTests extends PHPUnit_Framework_TestCase
{

    protected $dao;
    protected $testObjects;
    protected $auxDao;
    protected $auxObject;
    protected $auxDao2;
    protected $auxObject2;
    protected $auxDao3;
    protected $auxObject3;
    protected $auxDao4;
    protected $auxObject4;

    protected function setUp()
    {

        $this->auxDao4 = new PostgreSQLUserDAO();

        $this->auxObject4 = new UserVO();
        $this->auxObject4->setLogin("John Zoidberg");

        $this->auxDao4->create($this->auxObject4);

        $this->auxDao3 = new PostgreSQLAreaDAO();

        $this->auxObject3 = new AreaVO();
        $this->auxObject3->setName("Deliverers");

        $this->auxDao3->create($this->auxObject3);

        $this->auxDao2 = new PostgreSQLProjectDAO();

        $this->auxObject2 = new ProjectVO();
        $this->auxObject2->setAreaId($this->auxObject3->getId());

        $this->auxDao2->create($this->auxObject2);

        $this->auxDao = new PostgreSQLIterationDAO();

        $this->auxObject = new IterationVO();
        $this->auxObject->setProjectId($this->auxObject2->getId());
        $this->auxObject->setName("Earth");
        $this->auxObject->setInit(date_create("2100-01-01"));

        $this->auxDao->create($this->auxObject);

        $this->dao = new PostgreSQLStoryDAO();

        $this->testObjects[0] = new StoryVO();
        $this->testObjects[0]->setIterationId($this->auxObject->getId());
        $this->testObjects[0]->setName("Earth");
        $this->testObjects[0]->setAccepted(TRUE);
        $this->testObjects[0]->setUserId($this->auxObject4->getId());
        $this->testObjects[0]->setId(-1);

    }

    protected function tearDown()
    {
        foreach($this->testObjects as $testObject)
            $this->dao->delete($testObject);

        $this->auxDao->delete($this->auxObject);

        $this->auxDao2->delete($this->auxObject2);

        $this->auxDao3->delete($this->auxObject3);

        $this->auxDao4->delete($this->auxObject4);

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
        $this->testObjects[1]->setName("Mars");

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
        $this->testObjects[1]->setName("Mars");

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];
        $this->testObjects[2]->setName("Omicron Persei");

        $this->dao->create($this->testObjects[2]);

        $this->assertEquals($this->testObjects, $this->dao->getAll());

    }

    public function testGetByIterationId()
    {

        $this->testObjects[1] = clone $this->testObjects[0];
        $this->testObjects[1]->setName("Mars");

        $this->testObjects[2] = clone $this->testObjects[0];
        $this->testObjects[2]->setName("Omicron Persei");

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByIterationId($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetByIterationIdFromIteration()
    {

        $this->testObjects[1] = clone $this->testObjects[0];
        $this->testObjects[1]->setName("Mars");

        $this->testObjects[2] = clone $this->testObjects[0];
        $this->testObjects[2]->setName("Omicron Persei");

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->auxDao->getStories($this->auxObject->getId());

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

        $this->testObjects[0]->setAccepted(FALSE);

        $this->assertEquals($this->dao->update($this->testObjects[0]), 1);

    }

    public function testGetByIdAfterUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setAccepted(FALSE);

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
