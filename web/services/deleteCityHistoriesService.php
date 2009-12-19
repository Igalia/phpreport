<?php

   include_once('phpreport/web/services/WebServicesFunctions.php');
   include_once('phpreport/model/facade/UsersFacade.php');
   include_once('phpreport/model/vo/CityHistoryVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><cityHistories><cityHistory><id>71</id></cityHistory><cityHistory><id>72</id></cityHistory></cityHistories>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'cityHistories')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!user)
        {
            $string = "<return service='deleteCityHistories'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='deleteCityHistories'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            //print ($parser->name . "\n");

            if ($parser->name == "cityHistory")
            {

                $cityHistoryVO = new CityHistoryVO();

                $userAssignGroups = array();

                $parser->read();

                while ($parser->name != "cityHistory") {

                    //print ($parser->name . "\n");

                    switch ($parser->name ) {

                        case "id":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $cityHistoryVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $deleteCityHistories[] = $cityHistoryVO;

            }

        } while ($parser->read());

        //var_dump($deleteUsers);


        if (count($deleteCityHistories) >= 1)
            foreach((array)$deleteCityHistories as $cityHistory)
            {
                if (UsersFacade::DeleteCityHistory($cityHistory) == -1)
                {
                    $string = "<return service='deleteCityHistories'><error id='1'>There was some error while deleting the city history entries</error></return>";
                    break;
                }
            }



        if (!$string)
            $string = "<return service='deleteCityHistories'><ok>Operation Success!</ok></return>";

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
