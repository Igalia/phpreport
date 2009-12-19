<?php

   include_once('phpreport/web/services/WebServicesFunctions.php');
   include_once('phpreport/model/facade/UsersFacade.php');
   include_once('phpreport/model/vo/AreaHistoryVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><areaHistories><areaHistory><id>71</id></areaHistory><areaHistory><id>72</id></areaHistory></areaHistories>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'areaHistories')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!user)
        {
            $string = "<return service='deleteAreaHistories'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='deleteAreaHistories'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            //print ($parser->name . "\n");

            if ($parser->name == "areaHistory")
            {

                $areaHistoryVO = new AreaHistoryVO();

                $userAssignGroups = array();

                $parser->read();

                while ($parser->name != "areaHistory") {

                    //print ($parser->name . "\n");

                    switch ($parser->name ) {

                        case "id":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $areaHistoryVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $deleteAreaHistories[] = $areaHistoryVO;

            }

        } while ($parser->read());

        //var_dump($deleteUsers);


        if (count($deleteAreaHistories) >= 1)
            foreach((array)$deleteAreaHistories as $areaHistory)
            {
                if (UsersFacade::DeleteAreaHistory($areaHistory) == -1)
                {
                    $string = "<return service='deleteAreaHistories'><error id='1'>There was some error while deleting the area history entries</error></return>";
                    break;
                }
            }



        if (!$string)
            $string = "<return service='deleteAreaHistories'><ok>Operation Success!</ok></return>";

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
