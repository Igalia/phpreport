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


include_once(PHPREPORT_ROOT . '/model/vo/TaskSectionVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskSectionDAO/PostgreSQLTaskSectionDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/SectionVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/SectionDAO/PostgreSQLSectionDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ProjectDAO/PostgreSQLProjectDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/AreaDAO/PostgreSQLAreaDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ModuleVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ModuleDAO/PostgreSQLModuleDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/PostgreSQLUserDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/IterationVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/IterationDAO/PostgreSQLIterationDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/StoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/StoryDAO/PostgreSQLStoryDAO.php');

class PostgreSQLTaskSectionDAOBasicTests extends PHPUnit_Framework_TestCase
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
    protected $auxDao5;
    protected $auxObject5;

    protected function setUp()
    {

        $this->auxDao5 = new PostgreSQLUserDAO();

        $this->auxObject5 = new UserVO();
        $this->auxObject5->setLogin("John Zoidberg");

        $this->auxDao5->create($this->auxObject5);

        $this->auxDao4 = new PostgreSQLAreaDAO();

        $this->auxObject4 = new AreaVO();
        $this->auxObject4->setName("Deliverers");

        $this->auxDao4->create($this->auxObject4);

        $this->auxDao3 = new PostgreSQLProjectDAO();

        $this->auxObject3 = new ProjectVO();
        $this->auxObject3->setAreaId($this->auxObject4->getId());

        $this->auxDao3->create($this->auxObject3);

        $this->auxDao2 = new PostgreSQLModuleDAO();

        $this->auxObject2 = new ModuleVO();
        $this->auxObject2->setProjectId($this->auxObject3->getId());
        $this->auxObject2->setName("Earth");
        $this->auxObject2->setInit(date_create("2100-01-01"));

        $this->auxDao2->create($this->auxObject2);

        $this->auxDao = new PostgreSQLSectionDAO();

        $this->auxObject = new SectionVO();
        $this->auxObject->setModuleId($this->auxObject2->getId());
        $this->auxObject->setName("Earth");
        $this->auxObject->setUserId($this->auxObject5->getId());

        $this->auxDao->create($this->auxObject);

        $this->dao = new PostgreSQLTaskSectionDAO();

        $this->testObjects[0] = new TaskSectionVO();
        $this->testObjects[0]->setSectionId($this->auxObject->getId());
        $this->testObjects[0]->setName("Earth");
        $this->testObjects[0]->setEstHours(10);
        $this->testObjects[0]->setRisk(2);
        $this->testObjects[0]->setUserId($this->auxObject5->getId());
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

        $this->auxDao5->delete($this->auxObject5);

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

    public function testGetBySectionId()
    {

        $this->testObjects[1] = clone $this->testObjects[0];
        $this->testObjects[1]->setName("Mars");

        $this->testObjects[2] = clone $this->testObjects[0];
        $this->testObjects[2]->setName("Omicron Persei");

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getBySectionId($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetBySectionIdFromSection()
    {

        $this->testObjects[1] = clone $this->testObjects[0];
        $this->testObjects[1]->setName("Mars");

        $this->testObjects[2] = clone $this->testObjects[0];
        $this->testObjects[2]->setName("Omicron Persei");

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->auxDao->getTaskSections($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);
    }

    public function testGetByStoryId()
    {
        $auxDao = new PostgreSQLIterationDAO();

        $auxObject = new IterationVO();
        $auxObject->setProjectId($this->auxObject3->getId());
        $auxObject->setName("Earth");
        $auxObject->setInit(date_create("2100-01-01"));

        $auxDao->create($auxObject);

        $auxDao2 = new PostgreSQLStoryDAO();

        $auxObject2 = new StoryVO();
        $auxObject2->setIterationId($auxObject->getId());
        $auxObject2->setName("Earth");
        $auxObject2->setUserId($this->auxObject5->getId());

        $auxDao2->create($auxObject2);

        $this->testObjects[1] = clone $this->testObjects[0];
        $this->testObjects[1]->setName("Mars");

        $this->testObjects[2] = clone $this->testObjects[0];
        $this->testObjects[2]->setName("Omicron Persei");

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByStoryId($auxObject2->getId());

        $this->assertEquals($read, $this->testObjects);

        $auxDao2->delete($auxObject2);
        $auxDao->delete($auxObject);

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

        $this->testObjects[0]->setRisk(0);

        $this->assertEquals($this->dao->update($this->testObjects[0]), 1);

    }

    public function testGetByIdAfterUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setRisk(0);

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
