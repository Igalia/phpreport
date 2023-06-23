<?php

namespace Phpreport\Tests\integration;

use PHPUnit\Framework\TestCase;

if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../');

include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

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

    public function cleanUpTasks(): void
    {
        $parameters[] = \ConfigurationParametersManager::getParameter('DB_HOST');
        $parameters[] = \ConfigurationParametersManager::getParameter('DB_PORT');
        $parameters[] = \ConfigurationParametersManager::getParameter('DB_USER');
        $parameters[] = \ConfigurationParametersManager::getParameter('DB_NAME');
        $parameters[] = \ConfigurationParametersManager::getParameter('DB_PASSWORD');
        $parameters[] = \ConfigurationParametersManager::getParameter('EXTRA_DB_CONNECTION_PARAMETERS');

        $connectionString = "host=$parameters[0] port=$parameters[1] user=$parameters[2] dbname=$parameters[3] password=$parameters[4] $parameters[5]";

        $connect = pg_connect($connectionString);

        $result = pg_query($connect, $query="DELETE FROM task");
        if ($result == NULL) error_log("ERROR: Could not run query: $query");
        var_dump($result);

        pg_freeresult($result);
    }
}
