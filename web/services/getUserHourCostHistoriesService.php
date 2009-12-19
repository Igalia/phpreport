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

        $hourCostHistories = UsersFacade::GetUserHourCostHistories($userLogin);


        $string = "<hourCostHistories";
        if ($userLogin!="")
            $string = $string . " login='" . $userLogin . "'";
        $string = $string . ">";

        foreach((array) $hourCostHistories as $hourCostHistory)
        {
            $string = $string . "<hourCostHistory><id>{$hourCostHistory->getId()}</id><hourCost>{$hourCostHistory->getHourCost()}</hourCost><init format='Y-m-d'>{$hourCostHistory->getInitDate()->format('Y-m-d')}</init><end format='Y-m-d'>{$hourCostHistory->getEndDate()->format('Y-m-d')}</end></hourCostHistory>";
        }

        $string = $string . "</hourCostHistories>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
