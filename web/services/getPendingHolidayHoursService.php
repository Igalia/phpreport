<?php

   include_once('phpreport/web/services/WebServicesFunctions.php');
   include_once('phpreport/model/facade/UsersFacade.php');
   include_once('phpreport/model/vo/UserVO.php');

    $userLogin = $_GET['uid'];

    $init = $_GET['init'];

    $end = $_GET['end'];

    $dateFormat = $_GET['dateFormat'];

    $sid = $_GET['sid'];

    do {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<report";
            if ($userLogin!="")
            $string = $string . " login='" . $userLogin . "'";
            if ($init!="")
                $string = $string . " init='" . $init . "'";
            if ($end!="")
                $string = $string . " end='" . $end . "'";
            $string = $string . "><error id='2'>You must be logged in</error></report>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<report";
            if ($userLogin!="")
            $string = $string . " login='" . $userLogin . "'";
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
        } else
            $init = "1900-01-01";

        $init = date_create($init);

        if ($end!="")
        {
        $endParse = date_parse_from_format($dateFormat, $end);

            $end = "{$endParse['year']}-{$endParse['month']}-{$endParse['day']}";

            $end = date_create($end);
        } else
        $end = new DateTime();

        if ($userId != "")
        {

        $userVO = new UserVO();

        $userVO->setLogin($userLogin);

        $report = UsersFacade::GetPendingHolidayHours($init, $end, $userVO);

        } else
        $report = UsersFacade::GetPendingHolidayHours($init, $end);


        $string = "<report";
        if ($userLogin!="")
        $string = $string . " login='" . $userLogin . "'";
        if ($init!="")
        $string = $string . " init='" . $init->format($dateFormat) . "'";
        if ($end!="")
        $string = $string . " end='" . $end->format($dateFormat) . "'";
        $string = $string . ">";

        foreach((array) $report as $key => $entry)
        {
        $string = $string . "<pendingHolidayHours login='{$key}'><hours>{$entry}</hours></pendingHolidayHours>";
        }

        $string = $string . "</report>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
