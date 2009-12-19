<?php

include_once('phpreport/model/vo/CommonEventVO.php');

class CommonEventVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
        {

        $this->VO = new CommonEventVO();

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

    public function testDateField()
        {

        $this->VO->setDate(date_create('1999-12-31'));

        $this->assertEquals($this->VO->getDate(), date_create('1999-12-31'));

        $this->VO->setDate(date_create('2999-12-31'));

        $this->assertEquals($this->VO->getDate(), date_create('2999-12-31'));

        }

    public function testCityIdField()
        {

        $this->VO->setCityId(2);

        $this->assertEquals($this->VO->getCityId(), 2);

        $this->VO->setCityId(45);

        $this->assertEquals($this->VO->getCityId(), 45);

        }

}
?>
