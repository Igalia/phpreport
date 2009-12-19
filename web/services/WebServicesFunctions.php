<?php

function escape_string($string)
{

    $newString = str_replace("&", "&amp;", $string);
    $newString = str_replace("<", "&lt;", $newString);
    $newString = str_replace(">", "&gt;", $newString);
    $newString = str_replace("'", "&apos;", $newString);
    $newString = str_replace("\"", "&quot;", $newString);

    return $newString;

}


function unescape_string($string)
{

    $newString = str_replace("&amp;", "&", $string);
    $newString = str_replace("&lt;", "<", $newString);
    $newString = str_replace("&gt;", ">", $newString);
    $newString = str_replace("&apos;", "'", $newString);
    $newString = str_replace("&quot;", "\"", $newString);

    return $newString;

}


function authenticate($login, $sid = NULL)
{
    if ($sid)
        session_id($sid);

    session_start();

    if (empty($_SESSION['user']))
        return NULL;
    elseif (($_SESSION['user']->getLogin()) != $login)
        return NULL;

    return $_SESSION['user'];

}
