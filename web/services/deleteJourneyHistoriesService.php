<?php

   include_once('phpreport/web/services/WebServicesFunctions.php');
   include_once('phpreport/model/facade/UsersFacade.php');
   include_once('phpreport/model/vo/JourneyHistoryVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><journeyHistories><journeyHistory><id>71</id></journeyHistory><journeyHistory><id>72</id></journeyHistory></journeyHistories>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'journeyHistories')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!user)
        {
            $string = "<return service='deleteJourneyHistories'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='deleteJourneyHistories'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            //print ($parser->name . "\n");

            if ($parser->name == "journeyHistory")
            {

                $journeyHistoryVO = new JourneyHistoryVO();

                $userAssignGroups = array();

                $parser->read();

                while ($parser->name != "journeyHistory") {

                    //print ($parser->name . "\n");

                    switch ($parser->name ) {

                        case "id":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $journeyHistoryVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $deleteJourneyHistories[] = $journeyHistoryVO;

            }

        } while ($parser->read());

        //var_dump($deleteUsers);


        if (count($deleteJourneyHistories) >= 1)
            foreach((array)$deleteJourneyHistories as $journeyHistory)
            {
                if (UsersFacade::DeleteJourneyHistory($journeyHistory) == -1)
                {
                    $string = "<return service='deleteJourneyHistories'><error id='1'>There was some error while deleting the journey history entries</error></return>";
                    break;
                }
            }



        if (!$string)
            $string = "<return service='deleteJourneyHistories'><ok>Operation Success!</ok></return>";

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
