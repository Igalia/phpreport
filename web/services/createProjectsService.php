<?php

   include_once('phpreport/web/services/WebServicesFunctions.php');
   include_once('phpreport/model/facade/ProjectsFacade.php');
   include_once('phpreport/model/vo/ProjectVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    //$request = "<projects login='jjjameson' sid='902b2f21b1bfbd5b389310b2de703ac7'><project><activation>true</activation><init format='Y-m-d'>2009-10-2</init><end format='Y-m-d'>2009-10-2</end><invoice>708</invoice><estHours>71</estHours><areaId>6</areaId><description>Test project</description><type>Test</type><movHours>12</movHours><schedType>Test schedule</schedType></project></projects>";

    $parser->XML($request);

    $newTask = TRUE;

    while ($parser->read()) {

    if ($parser->name == "tasks")
    {
        /* We check authentication and authorization */
        require_once('phpreport/util/LoginManager.php');

        $sid = $parser->getAttribute("sid");

        if (!LoginManager::isLogged($sid))
        {
            $string = "<return service='createProjects'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='createProjects'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

    }
    elseif ($parser->name == "project")
    {

        $projectVO = new ProjectVO();

        $parser->read();

        while ($parser->name != "project") {

            //print ($parser->name . "\n");

            switch ($parser->name ) {

                case "activation":$parser->read();
                        if ($parser->hasValue)
                        {
                            if (strtolower($parser->value) == "true")
                                $projectVO->setActivation(true);
                            else
                                $projectVO->setActivation(false);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                case "init":    $dateFormat = $parser->getAttribute("format");
                        if (is_null($dateFormat))
                            $dateFormat = "Y-m-d";
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $date = $parser->value;
                            $dateParse = date_parse_from_format($dateFormat, $date);
                            $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                            $projectVO->setInit(date_create($date));
                            $parser->next();
                            $parser->next();
                        }
                        break;

                case "end":    $dateFormat = $parser->getAttribute("format");
                        if (is_null($dateFormat))
                            $dateFormat = "Y-m-d";
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $date = $parser->value;
                            $dateParse = date_parse_from_format($dateFormat, $date);
                            $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                            $projectVO->setEnd(date_create($date));
                            $parser->next();
                            $parser->next();
                        }
                        break;

                case "invoice":$parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setInvoice($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                case "estHours":$parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setEstHours($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                case "description":$parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setDescription(unescape_string($parser->value));
                            $parser->next();
                            $parser->next();
                        }
                        break;

                case "areaId":$parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setAreaId($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                case "type":    $parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setType(unescape_string($parser->value));
                            $parser->next();
                            $parser->next();
                        }
                        break;

                case "movHours":    $parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setMovedHours($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                case "schedType":    $parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setSchedType(unescape_string($parser->value));
                            $parser->next();
                            $parser->next();
                        }
                        break;

                default:    $parser->next();
                        break;

            }


        }

        $createProjects[] = $projectVO;

    }

    }


    if (count($createProjects) >= 1)
        if (ProjectsFacade::CreateProjects($createProjects) == -1)
            $string = "<return service='createProjects'><error id='1'>There was some error while creating the projects</error></return>";


    if (!$string)
    $string = "<return service='createProjects'><ok>Operation Success!</ok></return>";


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
