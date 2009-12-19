<?php

include_once('phpreport/model/vo/UserVO.php');

class UserVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
        {

        $this->VO = new UserVO();

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

    public function testLoginField()
        {

        $this->VO->setLogin("bender");

        $this->assertEquals($this->VO->getLogin(), "bender");

        $this->VO->setLogin("fry");

        $this->assertEquals($this->VO->getLogin(), "fry");

        }

    public function testPasswordField()
        {

        $this->VO->setPassword("kiss my shiny metal ass");

        $this->assertEquals($this->VO->getPassword(), "kiss my shiny metal ass");

        $this->VO->setPassword("you meat-bag");

        $this->assertEquals($this->VO->getPassword(), "you meat-bag");

        }

    public function testGroupsField()
        {

        $groups = array("Fry", "Leela", "Bender", "Hubert");

        $this->VO->setGroups($groups);

        $this->assertEquals($this->VO->getGroups(), $groups);

        $groups[3] = "Zoidberg";

        $this->VO->setGroups($groups);

        $this->assertEquals($this->VO->getGroups(), $groups);

        }

}
?>
