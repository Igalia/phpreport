<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


/* Include the generic header */
define(PAGE_TITLE, "PhpReport - Login");
include("include/header.php");

include_once('phpreport/model/facade/UsersFacade.php');
include_once('phpreport/model/vo/UserVO.php');
require_once('phpreport/util/LoginManager.php');

/* There are Http authentication data: we try to log in*/
if (isset($_SERVER['PHP_AUTH_USER']))
    if(LoginManager::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))
        header("Location: tasks.php");
    else
        echo _("Incorrect login information");



/* There are POST data: we try to log in */
if(isset($_POST["enter"])) {
    if(LoginManager::login($_POST["login"], $_POST["password"]))
        header("Location: tasks.php");
    else
        echo _("Incorrect login information");
}
?>

<div id="content">
    <form method="post">

        <?php echo _("Login")?>
        <input type="text" name="login"/>
        <?php echo _("Password")?>
        <input type="password" name="password"/>

        <input type="submit" name="enter" value="<?php echo _("Enter")?>"/>

    </form>
</div>

<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
