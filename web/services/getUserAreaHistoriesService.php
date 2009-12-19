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

        $areaHistories = UsersFacade::GetUserAreaHistories($userLogin);


        $string = "<areaHistories";
        if ($userLogin!="")
            $string = $string . " login='" . $userLogin . "'";
        $string = $string . ">";

        foreach((array) $areaHistories as $areaHistory)
        {
            $string = $string . "<areaHistory><id>{$areaHistory->getId()}</id><areaId>{$areaHistory->getAreaId()}</areaId><init format='Y-m-d'>{$areaHistory->getInitDate()->format('Y-m-d')}</init><end format='Y-m-d'>{$areaHistory->getEndDate()->format('Y-m-d')}</end></areaHistory>";
        }

        $string = $string . "</areaHistories>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
