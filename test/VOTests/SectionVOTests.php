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


include_once(PHPREPORT_ROOT . '/model/vo/SectionVO.php');

class SectionVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
    {

        $this->VO = new SectionVO();

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

        $this->VO->setName('Mars');

        $this->assertEquals($this->VO->getName(), 'Mars');

        $this->VO->setName('Omicron Persei');

        $this->assertEquals($this->VO->getName(), 'Omicron Persei');

    }

    public function testAcceptedField()
    {

        $this->VO->setAccepted(TRUE);

        $this->assertTrue($this->VO->getAccepted());

        $this->VO->setAccepted(FALSE);

        $this->assertFalse($this->VO->getAccepted());

    }

    public function testModuleIdField()
    {

        $this->VO->setModuleId(2);

        $this->assertEquals($this->VO->getModuleId(), 2);

        $this->VO->setModuleId(45);

        $this->assertEquals($this->VO->getModuleId(), 45);

    }

    public function testUserIdField()
    {

        $this->VO->setUserId(2);

        $this->assertEquals($this->VO->getUserId(), 2);

        $this->VO->setUserId(45);

        $this->assertEquals($this->VO->getUserId(), 45);

    }

    public function testTextField()
    {

        $this->VO->setName('Mars');

        $this->assertEquals($this->VO->getName(), 'Mars');

        $this->VO->setName('Omicron Persei');

        $this->assertEquals($this->VO->getName(), 'Omicron Persei');

    }

}
?>
