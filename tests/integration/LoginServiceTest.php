<?php

use Phpreport\Tests\integration\LoginSetupTestCase;

if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../');

class LoginServiceTest extends LoginSetupTestCase {

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testSessionIdIsSet(): void
    {
        $this->assertEquals(26, strlen($this->sessionId));
    }
}
