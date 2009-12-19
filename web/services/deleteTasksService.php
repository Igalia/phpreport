<?php

   include_once('phpreport/web/services/WebServicesFunctions.php');
   include_once('phpreport/model/facade/TasksFacade.php');
   include_once('phpreport/model/vo/TaskVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

     /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><tasks><task><date>2009-12-01</date><id>124303</id></task></tasks>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'tasks')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        // We check authentication and authorization
        require_once('phpreport/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='deleteTasks'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='deleteTasks'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }


        do {

            if ($parser->name == "task")
            {

                $taskVO = new TaskVO();

                $taskVO->setTelework(false);

                $parser->read();

                while ($parser->name != "task") {

                    switch ($parser->name ) {

                        case "id":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }


                }

                $taskVO->setUserId($user->getId());

                $deleteTasks[] = $taskVO;

            }

        } while ($parser->read());


        if (count($deleteTasks) >= 1)
            if (TasksFacade::DeleteReports($deleteTasks) == -1)
                $string = "<return service='deleteTasks'><error id='1'>There was some error while deleting the tasks</error></return>";

        if (!$string)
            $string = "<return service='deleteTasks'><ok>Operation Success!</ok></return>";


    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
