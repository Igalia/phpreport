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


include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/facade/actionplugin/ActionPlugin.php');
include_once('phpreport/util/SimpleHttpRequest.php');
include_once('phpreport/util/ConfigurationParametersManager.php');
include_once('phpreport/util/UnknownParameterException.php');

class SendTaskToExternalPhpReportInstance extends ActionPlugin {

    public function __construct($action) {
        $this->pluggedAction = $action;
    }

    public function run($status) {
        if ($this->pluggedAction instanceof CreateReportAction) {
            if ($status)
                $this->sendTaskToExternalPhpReport($this->pluggedAction->getTaskVO());
        }
        // if the action doesn't belong to one of those classes,
        // we do nothing
    }

    private function sendTaskToExternalPhpReport(TaskVO $task) {
        $user = DAOFactory::getUserDAO()->getById($task->getUserId());

        //check if the task has to be sent to the external PhpReport
        if(!$this->taskHasToBeSent($task, $user)) {
            return;
        }

        //retrieve configuration parameters
        try {
            $url = ConfigurationParametersManager::getParameter('EXTERNAL_PHPREPORT_URL');
        }
        catch(UnknownParameterException $e) {
            error_log("External PhpReport plugin is not configured properly");
            return;
        }

        include('ExternalPhpReportConfiguration.php');

        //login to the external PhpReport
        $externalUser = $relationUsersWithExternalUsers[$user->getLogin()];
        $sessionId = $this->login($url,
            $externalUser["login"], $externalUser["password"]);
        if(!$sessionId) {
            return;
        }

        //setup XML to be sent
        $xml = $this->buildXML($task, $sessionId);


        //send task
        $this->createTask($url, $xml);
    }

    private function taskHasToBeSent(TaskVO $task, UserVO $user) {
        include('ExternalPhpReportConfiguration.php');
        if(isset($relationProjectsWithExternalProjects[$task->getProjectId()])
                && isset($relationUsersWithExternalUsers[$user->getLogin()])) {
            return true;
        }
        return false;
    }

    private function login($url, $login, $password) {
        //prepare request
        $request = new SimpleHttpRequest();
        $request->init();

        $request->setUrl($url."/web/services/loginService.php");
        $request->addParameter("login", $login);
        $request->addParameter("password", $password);
        //TODO: setup HTTP authentication if neccessary

        //perform the request
        $output = $request->doRequest();
        $request->close();
        if(!$output) {
            error_log("No response when logging into external PhpReport");
            return;
        }

        //study the response
        try {
            $xml = new SimpleXMLElement($output);
        }
        catch (Exception $e) {
            error_log("Error parsing login response from external PhpReport: "
                . $e->getMessage());
            return false;
        }

        if(isset($xml->error)) {
            error_log("Error logging into external PhpReport: ".$xml->error);
            return false;
        }
        if(!isset($xml->sessionId)) {
            error_log("Unspecified error logging into external PhpReport");
            return false;
        }

        return (string)$xml->sessionId;
    }

    private function convertSecondsToTimeString($seconds) {
        return floor($seconds/60) . ":" .
            str_pad((string)$seconds%60, 2, "0", STR_PAD_LEFT);
    }

    private function buildXML($task, $sessionId) {
        include('ExternalPhpReportConfiguration.php');

        $xml = new SimpleXMLElement("<tasks></tasks>");
        $xml['sid'] = $sessionId;

        $taskXML = $xml->addChild("task");
        $taskXML->addChild("date");
        $taskXML->addChild("endTime");
        $taskXML->addChild("initTime");
        $taskXML->addChild("customerId");
        $taskXML->addChild("projectId");
        $taskXML->addChild("ttype");
        $taskXML->addChild("story");
        $taskXML->addChild("text");
        $taskXML->addChild("telework");

        $taskXML->date =  $task->getDate()->format('Y-m-d');
        $taskXML->initTime = $this->convertSecondsToTimeString($task->getInit());
        $taskXML->endTime = $this->convertSecondsToTimeString($task->getEnd());
        $taskXML->customerId = $relationClientsWithExternalClients[
                $task->getCustomerId()];
        $taskXML->projectId = $relationProjectsWithExternalProjects[
                $task->getProjectId()];
        $taskXML->ttype = $task->getTtype();
        $taskXML->story = $task->getStory();
        $taskXML->text = $task->getText();
        $taskXML->telework = $task->getTelework() ? "true" : "false";

        return $xml->asXML();
    }

    function createTask($url, $xmlString) {
        //prepare request
        $request = new SimpleHttpRequest();
        $request->init();

        $request->setUrl($url."/web/services/createTasksService.php");
        $request->setupPost($xmlString);
        //TODO: setup HTTP authentication if neccessary

        //perform the request
        $output = $request->doRequest();
        $request->close();
        if(!$output) {
            error_log("No response when creating task in external PhpReport");
            return;
        }

        //study the response
        try {
            $xml = new SimpleXMLElement($output);
        }
        catch (Exception $e) {
            error_log("Error parsing response from external PhpReport: "
                . $e->getMessage());
            return;
        }

        if(isset($xml->error)) {
            error_log("Error creating task in external PhpReport: "
                . $xml->error);
            return;
        }
        if(!isset($xml->ok)) {
            error_log("Unspecified error creating task in external PhpReport");
            return;
        }
        error_log("Success sending task to external PhpReport");
        error_log("External PhpReport response: " . $output);
    }
}
