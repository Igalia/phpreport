<?php

namespace Phpreport\Tests\integration;

use PHPUnit\Framework\TestCase;

if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../');

class LoginSetupTestCase extends TestCase {

    public string $sessionId;
    public string $host = "http://phpreport-app:8000/";

    public function makeRequest($path, $params = null, $method = 'GET', $data = null)
    {
        $requestUri = $this->host . $path;
        if ($params) {
            $requestUri .= '?' . http_build_query($params);
        }
        $username = 'admin';
        $password = 'admin';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $requestUri);
        curl_setopt($ch, CURLOPT_SSH_COMPRESSION, true);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);

        if ($method == 'POST') {
            $headers = array(
                "Content-type: text/xml",
                "Content-length: " . strlen($data),
                "Connection: close",
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $requestUri
        ]);
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);
        return simplexml_load_string($result);
    }

    public function setUp(): void
    {
        $xml = $this->makeRequest("/web/services/loginService.php");
        $this->sessionId = $xml->sessionId;
    }
}
