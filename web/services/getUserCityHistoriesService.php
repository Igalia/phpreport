<?php

   include_once('phpreport/web/services/WebServicesFunctions.php');
   include_once('phpreport/model/facade/UsersFacade.php');
   include_once('phpreport/model/vo/AreaHistoryVO.php');

    $userLogin = $_GET['uid'];

    $sid = $_GET['sid'];

    do {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<report";
            if ($userLogin!="")
                $string = $string . " login='" . $userLogin . "'";
            $string = $string . "><error id='2'>You must be logged in</error></report>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<report";
            if ($userLogin!="")
                $string = $string . " login='" . $userLogin . "'";
            $string = $string . "><error id='3'>Forbidden service for this User</error></report>";
            break;
        }

        $cityHistories = UsersFacade::GetUserCityHistories($userLogin);


        $string = "<cityHistories";
        if ($userLogin!="")
            $string = $string . " login='" . $userLogin . "'";
        $string = $string . ">";

        foreach((array) $cityHistories as $cityHistory)
        {
            $string = $string . "<cityHistory><id>{$cityHistory->getId()}</id><cityId>{$cityHistory->getCityId()}</cityId><init format='Y-m-d'>{$cityHistory->getInitDate()->format('Y-m-d')}</init><end format='Y-m-d'>{$cityHistory->getEndDate()->format('Y-m-d')}</end></cityHistory>";
        }

        $string = $string . "</cityHistories>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
