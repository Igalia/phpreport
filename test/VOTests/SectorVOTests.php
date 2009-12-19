<?php

include_once('phpreport/model/vo/SectorVO.php');

class SectorVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
        {

        $this->VO = new SectorVO();

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

        $this->VO->setName("PlanetExpress");

        $this->assertEquals($this->VO->getName(), "PlanetExpress");

        $this->VO->setName("Mommy industries");

        $this->assertEquals($this->VO->getName(), "Mommy industries");

        }

}
?>
