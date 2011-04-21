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

class CustomerVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
    {

        $this->VO = new CustomerVO();

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

        $this->VO->setName("Mommy");

        $this->assertEquals($this->VO->getName(), "Mommy");

        $this->VO->setName("Ogden Wernstrom");

        $this->assertEquals($this->VO->getName(), "Ogden Wernstrom");

    }

    public function testTypeField()
    {

        $this->VO->setType("Biggest industry on Earth");

        $this->assertEquals($this->VO->getType(), "Biggest industry on Earth");

        $this->VO->setType("Professor");

        $this->assertEquals($this->VO->getType(), "Professor");

    }

    public function testURLField()
    {

        $this->VO->setURL("www.mommyindustries.com");

        $this->assertEquals($this->VO->getURL(), "www.mommyindustries.com");

        $this->VO->setURL("www.marsuniversity.com/teaching/wernstrom");

        $this->assertEquals($this->VO->getURL(), "www.marsuniversity.com/teaching/wernstrom");

    }

    public function testSectorIdField()
    {

        $this->VO->setSectorId(2);

        $this->assertEquals($this->VO->getSectorId(), 2);

        $this->VO->setSectorId(45);

        $this->assertEquals($this->VO->getSectorId(), 45);

    }

}
?>
