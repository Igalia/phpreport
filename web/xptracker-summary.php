<?php

/* We check authentication and authorization */
require_once('phpreport/web/auth.php');

require_once('phpreport/model/facade/ProjectsFacade.php');
require_once('phpreport/model/vo/UserVO.php');
require_once('phpreport/model/vo/ProjectVO.php');

/* Include the generic header and sidebar*/
define("PAGE_TITLE", "PhpReport - XP tracker summary");
include("include/header.php");
include("include/sidebar.php");

?>

<link rel="stylesheet" type="text/css" href="include/ColumnNodeUI.css" />

<script type="text/javascript" src="include/ColumnNodeUI.js"></script>
<script type="text/javascript" src="include/TrackerSummaryTree.js"></script>

<script type="text/javascript">
Ext.onReady(function(){
<?php
    $user = $_SESSION['user'];

    // we gather all the active projects where the user is involved
    $projects = ProjectsFacade::GetProjectsByCustomerUserLogin(NULL, $user->getLogin(), true);

    // we print a TrackerSummaryTree for each project
    foreach((array) $projects as $project) {
        echo "new TrackerSummaryTree({projectId:".$project->getId().
             ",projectName:'".$project->getDescription()."'".
             ",user:'".$user->getLogin()."'})".
             ".render(Ext.get('content'));\n";
    }
?>
});

</script>

<div id="content">
</div>

<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
