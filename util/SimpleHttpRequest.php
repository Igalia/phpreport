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


/** File for ConfigurationParametersManager
 *
 *  This file just contains {@link SimpleHttpRequest}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage util
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

/** Class to provide a simple http request.
 *
 *  It uses libCURL internally, but it has a friendlier interface for the user.
 */
class SimpleHttpRequest {

    private $url;
    private $parametersArray;
    private $hasPost;
    private $postData;
    private $hasAuth;
    private $authLogin;
    private $authPassword;
    private $httpHeadersArray;
    private $curlHandle;

    public function __construct($url = null) {
        $this->url = $url;
        $this->parametersArray = array();
        $this->httpHeadersArray = array();
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getUrl() {
        return $this->url;
    }

    public function addParameter($name, $value) {
        $this->parametersArray[$name] = $value;
    }

    public function removeParameter($name) {
        unset($this->parametersArray[$name]);
    }

    public function getParameters() {
        return $this->parametersArray;
    }

    public function addHttpHeader($name, $value) {
        $this->httpHeadersArray[$name] = $value;
    }

    public function removeHttpHeader($name) {
        unset($this->httpHeadersArray[$name]);
    }

    public function getHttpHeaders() {
        return $this->httpHeadersArray;
    }

    public function setupPost($postData) {
        $this->hasPost = true;
        $this->postData = $postData;
    }

    public function clearPost() {
        if($this->hasPost) {
            curl_setopt($this->curlHandle, CURLOPT_POST, false);
            curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, null);
        }
    }

    public function setupHttpAuthentication($login, $password) {
        $this->hasAuth = true;
        $this->authLogin = $login;
        $this->authPassword = $password;
    }

    public function clearHttpAuthentication() {
        if($this->hasAuth) {
            curl_setopt($this->curlHandle, CURLOPT_HTTPAUTH, null);
            curl_setopt($this->curlHandle,CURLOPT_USERPWD, null);
        }
    }

    public function init() {
        // create curl resource
        $this->curlHandle = curl_init();
    }

    public function close() {
        curl_close($this->curlHandle);
    }

    public function doRequest() {
        if($this->url == null) {
            return false;
        }

        // set url
        $urlWithParameters = $this->url . $this->prepareParameters();
        curl_setopt($this->curlHandle, CURLOPT_URL, $urlWithParameters);

        curl_setopt($this->curlHandle, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);

        // set post data
        if($this->hasPost) {
            curl_setopt($this->curlHandle, CURLOPT_POST, true);
            curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $this->postData);
        }

        // set auth data
        if($this->hasAuth) {
            curl_setopt($this->curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($this->curlHandle,CURLOPT_USERPWD,
                $this->authLogin.":".$this->authPassword);
        }

        // set HTTP headers
        if(count($this->httpHeadersArray) > 0) {
            curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $this->prepareHttpHeaders());
        }

        // return the transfer as a string
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($this->curlHandle);

        return $output;
    }


    public function doRequestXML() {
        try {
            return new SimpleXMLElement($this->doRequest());
        }
        catch (Exception $e) {
            return null;
        }
    }

    private function prepareParameters() {
        if(count($this->parametersArray) == 0) {
            return "";
        }
        $parameterString = "?";
        foreach($this->parametersArray as $name => $value) {
            $parameterString .= $name . "=" . $value. "&";
        }
        return $parameterString;
    }

    private function prepareHttpHeaders() {
        $formattedHttpHeadersArray = array();
        foreach($this->httpHeadersArray as $name => $value) {
            $formattedHttpHeadersArray[] = $name . ": " . $value;
        }
        return $formattedHttpHeadersArray;
    }
}
