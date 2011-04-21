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
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/PostgreSQLUserDAO.php');

class PostgreSQLUserDAOBasicTests extends PHPUnit_Framework_TestCase
{

    protected $dao;
    protected $testObjects;

    protected function setUp()
    {

        $this->dao = new PostgreSQLUserDAO();

        $this->testObjects[0] = new UserVO();
        $this->testObjects[0]->setLogin("bender");
        $this->testObjects[0]->setId(-1);

    }

    protected function tearDown()
    {
        foreach($this->testObjects as $testObject)
            $this->dao->delete($testObject);

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

        $this->testObjects[1]->setLogin("flexo");

        $this->dao->create($this->testObjects[1]);

        $this->assertGreaterThan($this->testObjects[0]->getId(), $this->testObjects[1]->getId());

    }

    public function testGetById()
    {

        $this->dao->create($this->testObjects[0]);

        $read = $this->dao->getById($this->testObjects[0]->getId());

        $this->assertEquals($read, $this->testObjects[0]);

    }

    public function testLogin()
    {

        $this->dao->create($this->testObjects[0]);

        $read = $this->dao->login($this->testObjects[0]->getLogin(), NULL);

        $this->assertEquals($read, $this->testObjects[0]);

    }

    /**
      * @expectedException IncorrectLoginException
      */
    public function testIncorrectLogin()
    {

        $this->dao->create($this->testObjects[0]);

        $read = $this->dao->login($this->testObjects[0]->getLogin(), 'zoidberg');

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

        $this->testObjects[1]->setLogin("flexo");

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setLogin("roberto");

        $this->dao->create($this->testObjects[2]);

        $this->assertEquals($this->testObjects, $this->dao->getAll());

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

    public function testGetByLoginAfterDelete()
    {

        $this->dao->create($this->testObjects[0]);

        $this->dao->delete($this->testObjects[0]);

        $read = $this->dao->getByUserLogin($this->testObjects[0]->getLogin());

        $this->assertNull($read);

    }

    public function testGetByUserLogin()
    {

        $this->dao->create($this->testObjects[0]);

        $read = $this->dao->getByUserLogin($this->testObjects[0]->getLogin());

        $this->assertEquals($read, $this->testObjects[0]);

    }

    public function testGetByUserLoginNonExistent()
    {

        $read = $this->dao->getByUserLogin('qwertyuiopasdfghjkl');

        $this->assertNull($read);

    }

    public function testUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setPassword(NULL);

        $this->assertEquals($this->dao->update($this->testObjects[0]), 1);

    }

    public function testGetByIdAfterUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setPassword(NULL);

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
