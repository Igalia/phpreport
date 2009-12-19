<?php

    $sid = $_GET['sid'];

    /* We check authentication and authorization */
    require_once('phpreport/util/LoginManager.php');
    if (!LoginManager::isLogged($sid))
        header('Location: login.php');
    if (!LoginManager::isAllowed($sid))
        require('forbidden.php');

?>
