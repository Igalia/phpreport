<?php

include_once('phpreport/model/vo/JourneyHistoryVO.php');

class JourneyHistoryVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
        {

        $this->VO = new JourneyHistoryVO();

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

    public function testJourneyField()
        {

        $this->VO->setJourney(2);

        $this->assertEquals($this->VO->getJourney(), 2);

        $this->VO->setJourney(4);

        $this->assertEquals($this->VO->getJourney(), 4);

        }

    public function testInitDateField()
        {

        $this->VO->setInitDate(date_create('1999-12-31'));

        $this->assertEquals($this->VO->getInitDate(), date_create('1999-12-31'));

        $this->VO->setInitDate(date_create('2999-12-31'));

        $this->assertEquals($this->VO->getInitDate(), date_create('2999-12-31'));

        }

    public function testEndDateField()
        {

        $this->VO->setEndDate(date_create('1999-12-31'));

        $this->assertEquals($this->VO->getEndDate(), date_create('1999-12-31'));

        $this->VO->setEndDate(NULL);

        $this->assertEquals($this->VO->getEndDate(), NULL);

        }

    public function testUserIdField()
        {

        $this->VO->setUserId(2);

        $this->assertEquals($this->VO->getUserId(), 2);

        $this->VO->setUserId(45);

        $this->assertEquals($this->VO->getUserId(), 45);

        }

}
?>
