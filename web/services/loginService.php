<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */

/** login web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
    include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
    include_once(PHPREPORT_ROOT . '/util/LoginManager.php');

    /* Allow login only via HTTP Authentication data only if both username and password are not empty*/
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        header('WWW-Authenticate: Basic realm="phpeport Authentication"');
        header('HTTP/1.0 401 Unauthorized');
        http_response_code(401);
        $userLogin = false;
        $userPassword = false;
    } else {
        $userLogin = $_SERVER['PHP_AUTH_USER'];
        $userPassword = $_SERVER['PHP_AUTH_PW'];
    }

    $string = "";

    try{
        session_start();

        if (strtolower(ConfigurationParametersManager::getParameter('USE_EXTERNAL_AUTHENTICATION')) === 'true') {
            $clientId = ConfigurationParametersManager::getParameter('OIDC_CLIENT_ID');
            $clientSecret = ConfigurationParametersManager::getParameter('JWT_SECRET');
            $requestUri = ConfigurationParametersManager::getParameter('OIDC_TOKEN_ENDPOINT');
            $ch = curl_init();
            $params = "username=" . $userLogin . "&password=" . $userPassword . "&grant_type=password&client_id=" . $clientId . "&client_secret=" . $clientSecret;
            $headers = array(
                "Content-type: application/x-www-form-urlencoded"
            );

            curl_setopt($ch, CURLOPT_URL, $requestUri);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $decoded = json_decode($result, true);

            curl_close($ch);

            $api_token = $decoded["access_token"];
            $refresh_token = $decoded["refresh_token"];

            $oidc = LoginManager::setupOidcClient();
            $intro_token = $oidc->introspectToken(
                $api_token,
                null,
                $clientId,
                $clientSecret
            );

            $user = UsersFacade::GetUserByLogin($intro_token->username);
            if (!$user)
                throw new IncorrectLoginException("User not found");

            unset($_SESSION['api_token']);
            $_SESSION['api_token'] = $api_token;
            unset($_SESSION['refresh_token']);
            $_SESSION['refresh_token'] = $refresh_token;

        } else {
            $user = UsersFacade::Login($userLogin, $userPassword);
        }
        unset($_SESSION['user']);
        $_SESSION['user'] = $user;

        $sessionId = session_id();

        $string = $string . "<login><sessionId>$sessionId</sessionId></login>";

    }
    catch(IncorrectLoginException $exc){

    $string = $string . "<login><error id='1'>" . $exc->getMessage() . "</error></login>";

    }

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
