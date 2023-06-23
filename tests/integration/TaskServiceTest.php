<?php

use Phpreport\Tests\integration\LoginSetupTestCase;

class TaskServiceTest extends LoginSetupTestCase {

    public function setUp(): void
    {
        parent::setUp();
        parent::cleanUpTasks();
    }

    public function testCreateTaskFailsWithUserLoggedOut(): void
    {
        $res = $this->makeRequest('web/services/createTasksService.php', null, 'POST', '');
        $this->assertEquals('You must be logged in', $res->error);
    }

    public function testCreateTask(): void
    {
        $request = '<?xml version="1.0" encoding="ISO-8859-15"?>';
        $request .= '<tasks sid="'. $this->sessionId .'">';
        $request .= '<task><date>2023-03-24</date><initTime>00:00</initTime><endTime>03:00</endTime>';
        $request .= '<customerId>1</customerId><projectId>1</projectId></task></tasks>';
        $res = $this->makeRequest('web/services/createTasksService.php', null, 'POST', $request);
        $this->assertEquals('Operation Success!', $res->ok);
        $this->assertEquals('2023-03-24', (string)$res->tasks->task->date);
    }

    public function testCreateTaskFailsForOverlappingTimes(): void
    {
        $request = '<?xml version="1.0" encoding="ISO-8859-15"?>';
        $request .= '<tasks sid="'. $this->sessionId .'">';
        $request .= '<task><date>2023-03-24</date><initTime>00:00</initTime><endTime>03:00</endTime>';
        $request .= '<customerId>1</customerId><projectId>1</projectId></task></tasks>';
        $res = $this->makeRequest('web/services/createTasksService.php', null, 'POST', $request);

        // Creating a task with the same values of the previous test should fail
        // because they overlap.
        $request = '<?xml version="1.0" encoding="ISO-8859-15"?>';
        $request .= '<tasks sid="'. $this->sessionId .'">';
        $request .= '<task><date>2023-03-24</date><initTime>00:00</initTime><endTime>03:00</endTime>';
        $request .= '<customerId>1</customerId><projectId>1</projectId></task></tasks>';
        $res = $this->makeRequest('web/services/createTasksService.php', null, 'POST', $request);
        $this->assertStringContainsString('Task creation failed', (string)$res->errors->error);
        $this->assertStringContainsString('Detected overlapping times.', (string)$res->errors->error);
    }
}
