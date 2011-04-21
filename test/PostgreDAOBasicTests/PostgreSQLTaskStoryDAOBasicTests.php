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


include_once(PHPREPORT_ROOT . '/model/vo/TaskStoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskStoryDAO/PostgreSQLTaskStoryDAO.php');
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
include_once(PHPREPORT_ROOT . '/model/vo/ModuleVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ModuleDAO/PostgreSQLModuleDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskSectionVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskSectionDAO/PostgreSQLTaskSectionDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/SectionVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/SectionDAO/PostgreSQLSectionDAO.php');

class PostgreSQLTaskStoryDAOBasicTests extends PHPUnit_Framework_TestCase
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

        $this->auxDao2 = new PostgreSQLIterationDAO();

        $this->auxObject2 = new IterationVO();
        $this->auxObject2->setProjectId($this->auxObject3->getId());
        $this->auxObject2->setName("Earth");
        $this->auxObject2->setInit(date_create("2100-01-01"));

        $this->auxDao2->create($this->auxObject2);

        $this->auxDao = new PostgreSQLStoryDAO();

        $this->auxObject = new StoryVO();
        $this->auxObject->setIterationId($this->auxObject2->getId());
        $this->auxObject->setName("Earth");
        $this->auxObject->setUserId($this->auxObject5->getId());

        $this->auxDao->create($this->auxObject);

        $this->dao = new PostgreSQLTaskStoryDAO();

        $this->testObjects[0] = new TaskStoryVO();
        $this->testObjects[0]->setStoryId($this->auxObject->getId());
        $this->testObjects[0]->setName("Earth");
        $this->testObjects[0]->setEstHours(10);
        $this->testObjects[0]->setRisk(2);
        $this->testObjects[0]->setInit(date_create("2100-01-01"));
        $this->testObjects[0]->setEstEnd(date_create("2200-01-01"));
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
        $auxDao = new PostgreSQLModuleDAO();

        $auxObject = new ModuleVO();
        $auxObject->setProjectId($this->auxObject3->getId());
        $auxObject->setName("Earth");
        $auxObject->setInit(date_create("2100-01-01"));

        $auxDao->create($auxObject);

        $auxDao2 = new PostgreSQLSectionDAO();

        $auxObject2 = new SectionVO();
        $auxObject2->setModuleId($auxObject->getId());
        $auxObject2->setName("Earth");
        $auxObject2->setUserId($this->auxObject5->getId());

        $auxObject3 = new SectionVO();
        $auxObject3->setModuleId($auxObject->getId());
        $auxObject3->setName("Mars");
        $auxObject3->setUserId($this->auxObject5->getId());

        $auxDao2->create($auxObject2);
        $auxDao2->create($auxObject3);

        $auxDao3 = new PostgreSQLTaskSectionDAO();

        $auxObject4 = new TaskSectionVO();
        $auxObject4->setSectionId($auxObject2->getId());
        $auxObject4->setName("Earth");
        $auxObject4->setEstHours(10);
        $auxObject4->setRisk(2);
        $auxObject4->setUserId($this->auxObject5->getId());
        $auxObject4->setId(-1);

        $auxObject5 = new TaskSectionVO();
        $auxObject5->setSectionId($auxObject3->getId());
        $auxObject5->setName("Earth");
        $auxObject5->setEstHours(10);
        $auxObject5->setRisk(2);
        $auxObject5->setUserId($this->auxObject5->getId());
        $auxObject5->setId(-1);

        $auxDao3->create($auxObject4);
        $auxDao3->create($auxObject5);

        $this->testObjects[0]->setTaskSectionId($auxObject4->getId());

        $this->testObjects[1] = clone $this->testObjects[0];
        $this->testObjects[1]->setName("Mars");
        $this->testObjects[1]->setTaskSectionId($auxObject5->getId());

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);

        $read = $this->dao->getBySectionId($auxObject2->getId());

        $this->assertEquals(sizeof($read), 1);
        $this->assertEquals($read[0], $this->testObjects[0]);

        $this->dao->delete($this->testObjects[0]);
        $this->dao->delete($this->testObjects[1]);

        $auxDao3->delete($auxObject5);
        $auxDao3->delete($auxObject4);
        $auxDao2->delete($auxObject3);
        $auxDao2->delete($auxObject2);
        $auxDao->delete($auxObject);

    }

    public function testGetByTaskSectionId()
    {
        $auxDao = new PostgreSQLModuleDAO();

        $auxObject = new ModuleVO();
        $auxObject->setProjectId($this->auxObject3->getId());
        $auxObject->setName("Earth");
        $auxObject->setInit(date_create("2100-01-01"));

        $auxDao->create($auxObject);

        $auxDao2 = new PostgreSQLSectionDAO();

        $auxObject2 = new SectionVO();
        $auxObject2->setModuleId($auxObject->getId());
        $auxObject2->setName("Earth");
        $auxObject2->setUserId($this->auxObject5->getId());

        $auxObject3 = new SectionVO();
        $auxObject3->setModuleId($auxObject->getId());
        $auxObject3->setName("Mars");
        $auxObject3->setUserId($this->auxObject5->getId());

        $auxDao2->create($auxObject2);
        $auxDao2->create($auxObject3);

        $auxDao3 = new PostgreSQLTaskSectionDAO();

        $auxObject4 = new TaskSectionVO();
        $auxObject4->setSectionId($auxObject2->getId());
        $auxObject4->setName("Earth");
        $auxObject4->setEstHours(10);
        $auxObject4->setRisk(2);
        $auxObject4->setUserId($this->auxObject5->getId());
        $auxObject4->setId(-1);

        $auxObject5 = new TaskSectionVO();
        $auxObject5->setSectionId($auxObject3->getId());
        $auxObject5->setName("Earth");
        $auxObject5->setEstHours(10);
        $auxObject5->setRisk(2);
        $auxObject5->setUserId($this->auxObject5->getId());
        $auxObject5->setId(-1);

        $auxDao3->create($auxObject4);
        $auxDao3->create($auxObject5);

        $this->testObjects[0]->setTaskSectionId($auxObject4->getId());

        $this->testObjects[1] = clone $this->testObjects[0];
        $this->testObjects[1]->setName("Mars");
        $this->testObjects[1]->setTaskSectionId($auxObject5->getId());

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);

        $read = $this->dao->getByTaskSectionId($auxObject4->getId());

        $this->assertEquals(sizeof($read), 1);
        $this->assertEquals($read[0], $this->testObjects[0]);

        $this->dao->delete($this->testObjects[0]);
        $this->dao->delete($this->testObjects[1]);

        $auxDao3->delete($auxObject5);
        $auxDao3->delete($auxObject4);
        $auxDao2->delete($auxObject3);
        $auxDao2->delete($auxObject2);
        $auxDao->delete($auxObject);

    }

    public function testGetByStoryId()
    {

        $this->testObjects[1] = clone $this->testObjects[0];
        $this->testObjects[1]->setName("Mars");

        $this->testObjects[2] = clone $this->testObjects[0];
        $this->testObjects[2]->setName("Omicron Persei");

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByStoryId($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetByStoryIdFromStory()
    {

        $this->testObjects[1] = clone $this->testObjects[0];
        $this->testObjects[1]->setName("Mars");

        $this->testObjects[2] = clone $this->testObjects[0];
        $this->testObjects[2]->setName("Omicron Persei");

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->auxDao->getTaskStories($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);
    }

    public function testGetOpen()
    {

        $auxObject = new UserVO();
        $auxObject->setLogin("Zapp Brannigan");

        $this->auxDao5->create($auxObject);

        $auxObject2 = new ProjectVO();
        $auxObject2->setAreaId($this->auxObject4->getId());

        $this->auxDao3->create($auxObject2);

        $auxObject3 = new IterationVO();
        $auxObject3->setProjectId($auxObject2->getId());
        $auxObject3->setName("Mars");
        $auxObject3->setInit(date_create("2100-01-01"));

        $this->auxDao2->create($auxObject3);

        $auxObject4 = new StoryVO();
        $auxObject4->setIterationId($auxObject3->getId());
        $auxObject4->setName("Mars");
        $auxObject4->setUserId($this->auxObject5->getId());

        $this->auxDao->create($auxObject4);

        $this->testObjects[1] = clone $this->testObjects[0];
        $this->testObjects[1]->setName("Mars");
        $this->testObjects[1]->setEnd(date_create("2100-01-01"));

        $this->testObjects[2] = clone $this->testObjects[0];
        $this->testObjects[2]->setName("Omicron Persei");
        $this->testObjects[2]->setUserId($auxObject->getId());

        $this->testObjects[3] = clone $this->testObjects[0];
        $this->testObjects[3]->setName("Globetrotter Planet");
        $this->testObjects[3]->setStoryId($auxObject4->getId());

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);
        $this->dao->create($this->testObjects[3]);

        $read = $this->dao->getOpen($this->auxObject5->getId(), $this->auxObject3->getId());

        $this->assertEquals(sizeof($read), 1);
        $this->assertEquals($read[0], $this->testObjects[0]);

        $this->dao->delete($this->testObjects[0]);
        $this->dao->delete($this->testObjects[1]);
        $this->dao->delete($this->testObjects[2]);
        $this->dao->delete($this->testObjects[3]);

        $this->auxDao->delete($auxObject4);
        $this->auxDao2->delete($auxObject3);
        $this->auxDao3->delete($auxObject2);
        $this->auxDao5->delete($auxObject);
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
