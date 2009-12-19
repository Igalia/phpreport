<?php

    include_once('phpreport/web/services/WebServicesFunctions.php');
    include_once('phpreport/model/facade/CoordinationFacade.php');

    $iterationId = $_GET['iid'];

    $login = $_GET['login'];

    $sid = $_GET['sid'];


    do {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<stories iid='" . $iterationId . "'><error id='2'>You must be logged in</error></stories>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<stories iid='" . $iterationId . "'><error id='3'>Forbidden service for this User</error></stories>";
            break;
        }

        $stories = CoordinationFacade::GetIterationCustomStories($iterationId);

            $string = "<stories iid='" . $iterationId . "'>";


        foreach((array) $stories as $story)
        {

        $string = $string . "<story><id>{$story->getId()}</id><accepted>{$story->getAccepted()}</accepted><name>{$story->getName()}</name><estHours>{$story->getEstHours()}</estHours><spent>{$story->getSpent()}</spent><done>{$story->getDone()}</done><overrun>{$story->getOverrun()}</overrun><toDo>{$story->getToDo()}</toDo><nextStoryId>{$story->getNextStoryId()}</nextStoryId><developers>";

        $developers = $story->getDevelopers();

        foreach ((array)$developers as $developer)
        {

            $string = $string . "<developer><id>{$developer->getId()}</id><login>{$developer->getLogin()}</login></developer>";

        }

        $string = $string . "</developers><reviewer>";

        $reviewer = $story->getReviewer();

        if ($reviewer)
            $string = $string . "<id>{$reviewer->getId()}</id><login>{$reviewer->getLogin()}</login>";

        $string = $string . "</reviewer></story>";

        }

        $string = $string . "</stories>";

    } while (False);

    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
