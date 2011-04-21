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


include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CustomerDAO/PostgreSQLCustomerDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/SectorVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/SectorDAO/PostgreSQLSectorDAO.php');

class PostgreSQLCustomerDAOBasicTests extends PHPUnit_Framework_TestCase
{

    protected $dao;
    protected $testObjects;
    protected $auxDao;
    protected $auxObject;

    protected function setUp()
    {

        $this->auxDao = new PostgreSQLSectorDAO();

        $this->auxObject = new SectorVO();
        $this->auxObject->setName("Industry");

        $this->auxDao->create($this->auxObject);

        $this->dao = new PostgreSQLCustomerDAO();

        $this->testObjects[0] = new CustomerVO();
        $this->testObjects[0]->setSectorId($this->auxObject->getId());
        $this->testObjects[0]->setName("Mommy");
        $this->testObjects[0]->setType("Biggest industry on Earth");
        $this->testObjects[0]->setURL("www.mommyindustries.com");
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

    public function testGetBySectorId()
    {

        $this->testObjects[1] = new CustomerVO();
        $this->testObjects[1]->setSectorId($this->auxObject->getId());
        $this->testObjects[1]->setName("Mommy");
        $this->testObjects[1]->setType("Biggest industry on Earth");
        $this->testObjects[1]->setURL("www.mommyindustries.com");
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new CustomerVO();
        $this->testObjects[2]->setSectorId($this->auxObject->getId());
        $this->testObjects[2]->setName("Mommy");
        $this->testObjects[2]->setType("Biggest industry on Earth");
        $this->testObjects[2]->setURL("www.mommyindustries.com");
        $this->testObjects[2]->setId(-1);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getBySectorId($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetBySectorIdFromSector()
    {

        $this->testObjects[1] = new CustomerVO();
        $this->testObjects[1]->setSectorId($this->auxObject->getId());
        $this->testObjects[1]->setName("Mommy");
        $this->testObjects[1]->setType("Biggest industry on Earth");
        $this->testObjects[1]->setURL("www.mommyindustries.com");
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new CustomerVO();
        $this->testObjects[2]->setSectorId($this->auxObject->getId());
        $this->testObjects[2]->setName("Mommy");
        $this->testObjects[2]->setType("Biggest industry on Earth");
        $this->testObjects[2]->setURL("www.mommyindustries.com");
        $this->testObjects[2]->setId(-1);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->auxDao->getCustomers($this->auxObject->getId());

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

        $this->testObjects[0]->setURL(NULL);

        $this->assertEquals($this->dao->update($this->testObjects[0]), 1);

    }

    public function testGetByIdAfterUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setURL(NULL);

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
