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


include_once('phpreport/model/facade/actionplugin/ActionPlugin.php');
include_once('phpreport/util/SimpleHttpRequest.php');
include_once('phpreport/util/ConfigurationParametersManager.php');
include_once('phpreport/util/UnknownParameterException.php');

class SendTaskToNavalPlanPlugin extends ActionPlugin {

    public function __construct($action) {
        $this->pluggedAction = $action;
    }

    public function run($status) {
        if ($this->pluggedAction instanceof CreateReportAction) {
            if ($status)
                $this->sendTasksToNavalPlan();
        }
        // if the action doesn't belong to one of those classes,
        // we do nothing
    }

    private function sendTasksToNavalPlan() {
        //retrieve task
        $task = $this->pluggedAction->getTaskVO();

        //retrieve configuration parameters
        try {
            $serviceUrl = ConfigurationParametersManager::getParameter('NAVALPLAN_SERVICE_URL');
            $serviceUser = ConfigurationParametersManager::getParameter('NAVALPLAN_USER');
            $servicePassword = ConfigurationParametersManager::getParameter('NAVALPLAN_PASSWORD');
        }
        catch(UnknownParameterException $e) {
            error_log("NavalPlan plugin is not configured properly");
            return;
        }

        //setup HTTP request
        $request = new SimpleHttpRequest();
        $request->init();
        $request->setUrl($serviceUrl."/workreports");
        $request->setupHttpAuthentication($servicePassword, $serviceUser);
        $request->setupPost($this->convertToNavalPlanWorkReportLine($task));
        $request->addHttpHeader("Content-type", "application/xml");

        //perform request
        $output = $request->doRequest();

        try {
            $xml = new SimpleXMLElement($output);
        }
        catch (Exception $e) {
            error_log("Error parsing response from NavalPlan: ".$e->getMessage()."\n");
        }
        error_log("NavalPlan response: " . $output);
    }

    private function convertToNavalPlanWorkReportLine($task) {
        include("NavalPlanConfiguration.php");

        $xml = new SimpleXMLElement("<work-report-list></work-report-list>");
        $xml["xmlns"] = "http://rest.ws.navalplanner.org";

        $workReport = $xml->addChild("work-report");
        $workReport["work-report-type"] = $workReportTypeCode;
        $workReport["code"] = "phpreport-" . $task->getUserId() .
            "-" . $task->getDate()->format('Y-m-d');

        $linesList = $workReport->addChild("work-report-line-list");
        $line = $linesList->addChild("work-report-line");
        $line["hours"] = ceil(($task->getEnd() - $task->getInit()) / 60);
        $line["hour-type"] = $hourTypeCode;
        $line["work-order"] = $relationProjectsWithOrders[$task->getProjectId()];
        $line["resource"] = $relationWorkersWithResources[$task->getUserId()];
        $line["date"] = $task->getDate()->format('Y-m-d');
        $line["code"] = "phpreport-" . $task->getId();

        return $xml->asXML();
    }
}
