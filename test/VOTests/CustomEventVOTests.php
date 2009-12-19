<?php

include_once('phpreport/model/vo/CustomEventVO.php');

class CustomEventVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
        {

        $this->VO = new CustomEventVO();

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

    public function testHoursField()
        {

        $this->VO->setHours(2.5);

        $this->assertEquals($this->VO->getHours(), 2.5);

        $this->VO->setHours(4.5);

        $this->assertEquals($this->VO->getHours(), 4.5);

        }

    public function testDateField()
        {

        $this->VO->setDate(date_create('1999-12-31'));

        $this->assertEquals($this->VO->getDate(), date_create('1999-12-31'));

        $this->VO->setDate(date_create('2999-12-31'));

        $this->assertEquals($this->VO->getDate(), date_create('2999-12-31'));

        }

    public function testUserIdField()
        {

        $this->VO->setUserId(2);

        $this->assertEquals($this->VO->getUserId(), 2);

        $this->VO->setUserId(45);

        $this->assertEquals($this->VO->getUserId(), 45);

        }

    public function testTypeField()
        {

        $this->VO->setType("Feeding Nibbles");

        $this->assertEquals($this->VO->getType(), "Feeding Nibbles");

        $this->VO->setType("Hailing Zoidberg!");

        $this->assertEquals($this->VO->getType(), "Hailing Zoidberg!");

        }

}
?>
