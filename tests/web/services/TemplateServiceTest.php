<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Phpreport\Web\services\TemplateService as TemplateService;

define('PHPREPORT_ROOT', __DIR__ . '/../../../');

require_once(PHPREPORT_ROOT . '/util/LoginManager.php');


class LoginManagerMock extends \LoginManager
{
    public static function isLogged($sid = NULL)
    {
        if ($sid) {
            $userVO = new \UserVO();
            $userVO->setId($sid);
            return $userVO;
        }
        return NULL;
    }

    public static function isAllowed($sid = NULL)
    {
        return true;
    }
}

class TemplateServiceTest extends TestCase
{
    private \LoginManager $loginManagerMock;

    public function setUp(): void
    {
        $this->loginManagerMock = new LoginManagerMock();
        $this->instance = new TemplateService(
            $this->loginManagerMock
        );
    }

    public function testCannotCreateATemplateIfLoggedOut(): void
    {
        $request = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<templates>'
            . '<template>'
            . '<projectId>1</projectId>'
            . '<name>awesome template</name>'
            . '<ttype>community</ttype>'
            . '<story>piedpiper</story>'
            . '<taskStoryId>2</taskStoryId>'
            . '<telework>true</telework>'
            . '<onsite>false</onsite>'
            . '<text>fixing this crazy bug</text>'
            . '</template>'
            . '</templates>';

        $this->assertEquals(
            "<?xml version=\"1.0\"?>\n<return service=\"createTemplates\"><success>false</success><error id=\"2\">You must be logged in</error></return>\n",
            $this->instance->createTemplate($request)
        );
    }

    public function testCanParseRequestWithCorrectPayload(): void
    {
        $request = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<templates>'
            . '<template>'
            . '<projectId>1</projectId>'
            . '<name>awesome template</name>'
            . '<ttype>community</ttype>'
            . '<story>piedpiper</story>'
            . '<taskStoryId>3</taskStoryId>'
            . '<telework>true</telework>'
            . '<onsite>false</onsite>'
            . '<text>fixing this crazy bug</text>'
            . '<initTime>02:45</initTime>'
            . '<endTime>13:15</endTime>'
            . '</template>'
            . '</templates>';

        $parser = new \XMLReader();
        $parser->XML($request);

        $expectedTemplate = new \TemplateVO();
        $expectedTemplate->setStory('piedpiper');
        $expectedTemplate->setTelework(true);
        $expectedTemplate->setOnsite(false);
        $expectedTemplate->setUserId('456');
        $expectedTemplate->setTaskStoryId('3');
        $expectedTemplate->setTtype('community');
        $expectedTemplate->setName('awesome template');
        $expectedTemplate->setText('fixing this crazy bug');
        $expectedTemplate->setProjectId('1');
        $expectedTemplate->setInitTime(165);
        $expectedTemplate->setInitTimeRaw('02:45');
        $expectedTemplate->setEndTime(795);
        $expectedTemplate->setEndTimeRaw('13:15');

        $this->assertEquals(
            [$expectedTemplate],
            $this->instance->parseTemplates($parser, $userId = '456')
        );
    }
}
