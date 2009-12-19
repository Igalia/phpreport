<?php

/* Include the generic header */
define(PAGE_TITLE, "PhpReport - Login");
include("include/header.php");

include_once('phpreport/model/facade/UsersFacade.php');
include_once('phpreport/model/vo/UserVO.php');
require_once('phpreport/util/LoginManager.php');

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
