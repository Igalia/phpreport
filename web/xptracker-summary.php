<?php
/*
 * Copyright (C) 2009, 2013 Igalia, S.L. <info@igalia.com>
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

define('PHPREPORT_ROOT', __DIR__ . '/../');

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

require_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
require_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
require_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');

/* Include the generic header and sidebar*/
define("PAGE_TITLE", "PhpReport - XP tracker summary");
include("include/header.php");

/* Check GET parameters */
if(isset($_GET["projectId"])) {
    $projectId = $_GET["projectId"];
}

?>

<link rel="stylesheet" type="text/css" href="include/ColumnNodeUI.css" />
<script src="include/ColumnNodeUI.min.js"></script>
<script src="include/TrackerSummaryTree.min.js"></script>

<script>
Ext.onReady(function(){
<?php
    $user = $_SESSION['user'];

    if(isset($projectId)) {
        $projects = array(ProjectsFacade::GetProject($projectId));
    }
    else {
        // we gather all the active projects where the user is involved
        $projects = ProjectsFacade::GetProjectsByCustomerUserLogin(NULL, $user->getLogin(), true);
    }

    // we print a TrackerSummaryTree for each project
    foreach((array) $projects as $project) {
        echo "new TrackerSummaryTree({projectId:".$project->getId().
             ",projectName:'".$project->getDescription()."'".
             ",user:'".$user->getLogin()."'})".
             ".render(Ext.get('content'));\n";
    }
?>

//build the combo with all projects with ExtJS
new Ext.FormPanel({
    labelWidth: 150,
    frame: false,
    border: false,
    bodyStyle: "background:transparent; padding-top: 20px",
    renderTo: 'content',
    items: [{
        fieldLabel: 'Check another project:',
        xtype: 'combo',
        value: '',
        transform: 'projects',
        triggerAction:'all',
        forceSelection: false,
        autoSelect: false,
        lazyRender: true,
        renderTo: 'content',
        listeners: {
            'select': function () {
                var projectId = this.getValue();
                if(projectId)
                    window.location = 'xptracker-summary.php?projectId='
                            + projectId;
            }
        }
    }]
});

});
</script>

<div id="content">
    <select id="projects" name="projects" display="none">
        <?php
        //write projects list as options in the combo
        $projects = ProjectsFacade::GetAllProjects();
        foreach((array) $projects as $project) {
            echo "<option value=".$project->getId().">" .
                    $project->getDescription() .
                    "</option>";
        }
        ?>
    </select>
</div>

<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
