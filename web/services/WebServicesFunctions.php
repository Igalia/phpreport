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

require_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

function escape_string($string)
{
    return htmlentities($string, ENT_XML1);
}


function unescape_string($string)
{
    return html_entity_decode($string, ENT_XML1);
}


function authenticate($login, $sid = NULL)
{
    if ($sid)
        session_id($sid);

    session_start();

    if (empty($_SESSION['user']))
        return NULL;
    elseif (($_SESSION['user']->getLogin()) != $login)
        return NULL;

    return $_SESSION['user'];

}

function makeAPIRequest($path, $params = null, $method = 'GET', $data = null)
{
    $requestUri = ConfigurationParametersManager::getParameter('API_BASE_URL') . $path;
    $is_token_active = LoginManager::isTokenActive($_SESSION['api_token']);
    if (!$is_token_active) {
        try {
            LoginManager::refreshAccessToken();
        } catch (Exception $e) {
            return
                array(
                    'token_refresh_error' => array(
                        'msg' => $e->getMessage(),
                        'code' => $e->getCode()
                    )
                );
        }
    }
    $api_token = $_SESSION['api_token'];

    if ($params) {
        $requestUri .= '?' . http_build_query($params);
    }
    $ch = curl_init();
    $headers = array(
        "Content-type: application/json",
        "Authorization: Bearer " . $api_token,
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $requestUri);
    curl_setopt($ch, CURLOPT_SSH_COMPRESSION, true);

    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $requestUri
    ]);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }

    curl_close($ch);
    return json_decode($result);
}
