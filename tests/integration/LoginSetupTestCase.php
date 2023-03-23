<?php

namespace Phpreport\Tests\integration;

use PHPUnit\Framework\TestCase;

if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../');

class LoginSetupTestCase extends TestCase {

    public string $sessionId;
    public string $host = "http://phpreport-app:8000";

    public function makeRequest($path, $params = null)
    {
        $requestUri = $this->host . $path;
        if ($params) {
            $requestUri += '?' . http_build_query($params);
        }
        $username = 'admin';
        $password = 'admin';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $requestUri);
        curl_setopt($ch, CURLOPT_SSH_COMPRESSION, true);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);

        curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $requestUri
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return simplexml_load_string($result);
    }

    public function setUp(): void
    {
        $xml = $this->makeRequest("/web/services/loginService.php");
        $this->sessionId = $xml->sessionId;
    }
}
