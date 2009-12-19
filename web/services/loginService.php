<?php

   include_once('phpreport/web/services/WebServicesFunctions.php');
   include_once('phpreport/model/facade/UsersFacade.php');
   include_once('phpreport/model/vo/UserVO.php');

/**
 * HTTP PARAMETERS RECEIVED BY THIS PAGE:
 *
 * login = Login of the user
 * password = Password of the user
 */


    $userLogin = $_GET['login'];

    $userPassword = $_GET['password'];

    //$userLogin = 'jjjameson';

    //$userPassword = 'jaragunde';


    $string = "";

    try{

        $user = UsersFacade::Login($userLogin, $userPassword);

        session_start();

        $_SESSION['user'] = $user;

        $sessionId = session_id();

        $string = $string . "<login><sessionId>$sessionId</sessionId></login>";

    }
    catch(IncorrectLoginException $exc){

    $string = $string . "<login><error id='1'>" . $exc->getMessage() . "</error></login>";

    }

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
