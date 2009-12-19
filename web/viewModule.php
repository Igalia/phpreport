<?php

$moduleId = $_GET["mid"];

/* We check authentication and authorization */
require_once('phpreport/web/auth.php');

$module = CoordinationFacade::GetModule($moduleId);

/* Include the generic header and sidebar*/
define(PAGE_TITLE, "PhpReport - Module Data");
include_once("include/header.php");
include_once("include/sidebar.php");
include_once('phpreport/model/facade/CoordinationFacade.php');
include_once('phpreport/model/facade/ProjectsFacade.php');
include_once('phpreport/model/vo/ModuleVO.php');
include_once('phpreport/web/services/WebServicesFunctions.php');


?><link rel="stylesheet" type="text/css" href="include/ColumnNodeUI.css" />
<script type="text/javascript" src="include/ColumnNodeUI.js"></script>
<script type="text/javascript" src="include/AnalysisTrackerSummaryTree.js"></script>

<script type="text/javascript">

Ext.onReady(function(){

    // Main Panel
    var mainPanel = new Ext.FormPanel({
        width: 300,
        labelWidth: 75,
        frame:true,
        title: 'Module Data',
      bodyStyle: 'padding:5px 5px 0',
      defaults: {
             width: 300,
             labelStyle: 'text-align: right; width: 75; font-weight:bold; padding: 0 0 0 0;'
        },
      defaultType: 'displayfield',
        items:[{
            id:'name',
            name: 'name',
           fieldLabel:'Name',
        <?php
              echo "value:'" . $module->getName() . "'";
        ?>
        },{
            id:'description',
            name:'description',
                fieldLabel: 'Description',
        <?php
              echo "value:'" . $module->getSummary() . "'";
        ?>
        },{
            id: 'startDate',
              name:'startDate',
                fieldLabel: 'Start Date',
        <?php
                    echo "value:'" . $module->getInit()->format("d-m-Y") . "'";
        ?>
        },{
              id: 'endDate',
                name: 'endDate',
                fieldLabel: 'End Date',
        <?php
                    echo "value:'" . $module->getEnd()->format("d-m-Y") . "'";
        ?>
        }]
    });

    var trackerSummaryTree = new TrackerSummaryTree({
        <?php

        $project = ProjectsFacade::GetProject($module->getProjectId());

                echo "projectId:" . $project->getId() . ", projectName: '" . $project->getDescription()  .  "', user:'" . $_SESSION["user"]->getLogin() . "', moduleId: " . $module->getId();

        ?>
    });

    mainPanel.render(Ext.get("content"));

    trackerSummaryTree.render(Ext.get("content"));

});
</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
