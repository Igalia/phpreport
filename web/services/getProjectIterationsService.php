<?php

    include_once('phpreport/web/services/WebServicesFunctions.php');
    include_once('phpreport/model/facade/CoordinationFacade.php');

    $projectId = $_GET['pid'];

    $login = $_GET['login'];

    $sid = $_GET['sid'];


    do {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<iterations pid='" . $projectId . "'><error id='2'>You must be logged in</error></iterations>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<iterations pid='" . $projectId . "'><error id='3'>Forbidden service for this User</error></iterations>";
            break;
        }

        $iterations = CoordinationFacade::GetProjectIterations($projectId);

        $string = "<iterations pid='" . $projectId . "'>";


        foreach((array) $iterations as $iteration)
        {

        $string = $string . "<iteration><id>{$iteration->getId()}</id><name>{$iteration->getName()}</name><summary>{$iteration->getSummary()}</summary>";

        if (!is_null($iteration->getInit()))
            $string = $string . "<initDate format='Y-m-d'>{$iteration->getInit()->format("Y-m-d")}</initDate>";
        else    $string = $string . "<initDate/>";

        if (!is_null($iteration->getEnd()))
            $string = $string . "<endDate format='Y-m-d'>{$iteration->getEnd()->format("Y-m-d")}</endDate>";
        else    $string = $string . "<endDate/>";

        $string = $string . "</iteration>";

        }

        $string = $string . "</iterations>";

    } while (False);

    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
