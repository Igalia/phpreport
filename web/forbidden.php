<?php

/* Include the generic header and sidebar*/
define(PAGE_TITLE, "PhpReport - Forbidden page");
include("include/header.php");
include("include/sidebar.php");

echo _("You are not allowed to access this page");

/* Include the footer to close the header */
include("include/footer.php");

exit();
?>
