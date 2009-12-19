<?php

include_once('phpreport/model/vo/CustomerVO.php');

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
