<?php

    include_once('phpreport/web/services/WebServicesFunctions.php');
    include_once('phpreport/model/facade/CoordinationFacade.php');

    $sid = $_GET['sid'];

    $projectId = $_GET['pid'];

    if (strtolower($_GET['uidActive']) == 'true')
        $userIdActive = true;
    else
        $userIdActive = false;


    do {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<taskStories";
            if ($projectId != "")
                $string = $string . " pid='" . $projectId . "'";
            if ($userIdActive)
                $string = $string . " uidActive = 'True'";
            else
                $string = $string . " uidActive = 'False'";
            $string = $string . "><error id='2'>You must be logged in</error></taskStories>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<taskStories";
            if ($projectId != "")
                $string = $string . " pid='" . $projectId . "'";
            if ($userIdActive)
                $string = $string . " uidActive = 'True'";
            else
                $string = $string . " uidActive = 'False'";
            $string = $string . "><error id='3'>Forbidden service for this User</error></taskStories>";
            break;
        }

        if ($userIdActive)
            $userId = $_SESSION['user']->getId();

        $taskStories = CoordinationFacade::GetOpenTaskStories($userId, $projectId);

        $string = "<taskStories";
        if ($projectId != "")
            $string = $string . " pid='" . $projectId . "'";
        if ($userIdActive)
            $string = $string . " uidActive = 'True'>";
        else
            $string = $string . " uidActive = 'False'>";


        foreach((array) $taskStories as $taskStory)
        {

            $story = CoordinationFacade::GetStory($taskStory->getStoryId());

            $iteration = CoordinationFacade::GetIteration($story->getIterationId());

            $string = $string . "<taskStory><id>{$taskStory->getId()}</id><friendlyName>{$taskStory->getName()} - {$story->getName()} - {$iteration->getName()}</friendlyName></taskStory>";

        }

        $string = $string . "</taskStories>";

    } while (False);

    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
