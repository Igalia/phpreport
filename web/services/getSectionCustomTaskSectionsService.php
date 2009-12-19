<?php

    include_once('phpreport/web/services/WebServicesFunctions.php');
    include_once('phpreport/model/facade/CoordinationFacade.php');

    $sectionId = $_GET['scid'];

    $sid = $_GET['sid'];


    do {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<taskSections stid='" . $sectionId . "'><error id='2'>You must be logged in</error></taskSections>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<taskSections stid='" . $sectionId . "'><error id='3'>Forbidden service for this User</error></taskSections>";
            break;
        }

        $taskSections = CoordinationFacade::GetSectionCustomTaskSections($sectionId);

        $string = "<taskSections stid='" . $sectionId . "'>";


        foreach((array) $taskSections as $taskSection)
        {

        $string = $string . "<taskSection><id>{$taskSection->getId()}</id><risk>{$taskSection->getRisk()}</risk><name>{$taskSection->getName()}</name><estHours>{$taskSection->getEstHours()}</estHours><spent>{$taskSection->getSpent()}</spent><toDo>{$taskSection->getToDo()}</toDo><developer>";

        $developer = $taskSection->getDeveloper();

        if ($developer)
            $string = $string . "<id>{$developer->getId()}</id><login>{$developer->getLogin()}</login>";

        $string = $string . "</developer><reviewer>";

        $reviewer = $taskSection->getReviewer();

        if ($reviewer)
            $string = $string . "<id>{$reviewer->getId()}</id><login>{$reviewer->getLogin()}</login>";

        $string = $string . "</reviewer>";

        $string = $string . "</taskSection>";

        }

        $string = $string . "</taskSections>";

    } while (False);

    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
