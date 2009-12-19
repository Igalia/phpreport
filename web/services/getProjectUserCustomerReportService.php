<?php

   include_once('phpreport/web/services/WebServicesFunctions.php');
   include_once('phpreport/model/facade/TasksFacade.php');
   include_once('phpreport/model/vo/ProjectVO.php');

    $projectId = $_GET['pid'];

    $init = $_GET['init'];

    $end = $_GET['end'];

    $dateFormat = $_GET['dateFormat'];

    $login = $_GET['login'];

    $sid = $_GET['sid'];

    do {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<report pid='" . $projectId . "'";
            if ($init!="")
                $string = $string . " init='" . $init . "'";
            if ($end!="")
                $string = $string . " end='" . $end . "'";
            $string = $string . "><error id='2'>You must be logged in</error></report>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<report pid='" . $projectId . "'";
            if ($init!="")
                $string = $string . " init='" . $init . "'";
            if ($end!="")
                $string = $string . " end='" . $end . "'";
            $string = $string . "><error id='3'>Forbidden service for this User</error></report>";
            break;
        }

        if ($dateFormat=="")
        $dateFormat = "Y-m-d";

        if ($init!="")
        {
        $initParse = date_parse_from_format($dateFormat, $init);

            $init = "{$initParse['year']}-{$initParse['month']}-{$initParse['day']}";

        $init = date_create($init);

        } else $init = NULL;


        if ($end!="")
        {
        $endParse = date_parse_from_format($dateFormat, $end);

            $end = "{$endParse['year']}-{$endParse['month']}-{$endParse['day']}";

            $end = date_create($end);

        } else $end = NULL;

        $string = "<report pid='$projectId'>";

        $projectVO = new ProjectVO();

        $projectVO->setId($projectId);

        $report = TasksFacade::GetProjectUserCustomerReport($projectVO, $init, $end);

        foreach((array) $report as $login => $report2)
        {
        $string = $string . "<user login='$login'>";
        foreach((array) $report2 as $customer => $hours)
            $string = $string . "<workedHours cid='" . $customer . "'>$hours</workedHours>";
        $string = $string . "</user>";
        }

        $string = $string . "</report>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
