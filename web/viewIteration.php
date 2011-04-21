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

define('PHPREPORT_ROOT', __DIR__ . '/../');

$iterationId = $_GET["iid"];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Iteration Data");
include_once("include/header.php");
include_once("include/sidebar.php");
include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/IterationVO.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

$iteration = CoordinationFacade::GetIteration($iterationId);


?><link rel="stylesheet" type="text/css" href="include/ColumnNodeUI.css" />
<script type="text/javascript" src="include/ColumnNodeUI.js"></script>
<script type="text/javascript" src="include/TrackerSummaryTree.js"></script>

<script type="text/javascript">

Ext.onReady(function(){

    Ext.QuickTips.init();

    // Main Panel
    var mainPanel = new Ext.FormPanel({
        width: 300,
        labelWidth: 75,
        frame:true,
        title: 'Iteration Data',
      bodyStyle: 'padding:5px 5px 0',
        tools: [{
            id: 'edit',
            qtip: 'Edit this Iteration',
            handler: function(){
            <?php
                echo "window.location = 'iterationForm.php?iid={$iteration->getId()}';"
            ?>
            }
        }],

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
              echo "value:'" . $iteration->getName() . "'";
        ?>
        },{
            id:'description',
            name:'description',
                fieldLabel: 'Description',
        <?php
              echo "value:'" . $iteration->getSummary() . "'";
        ?>
        },{
            id: 'startDate',
              name:'startDate',
                fieldLabel: 'Start Date',
        <?php
                    echo "value:'" . $iteration->getInit()->format("d-m-Y") . "'";
        ?>
        },{
              id: 'endDate',
                name: 'endDate',
                fieldLabel: 'End Date',
        <?php
                    echo "value:'" . $iteration->getEnd()->format("d-m-Y") . "'";
        ?>
        }]
    });

    var trackerSummaryTree = new TrackerSummaryTree({
        <?php

        $project = ProjectsFacade::GetProject($iteration->getProjectId());

                echo "projectId:" . $project->getId() . ", projectName: '" . $project->getDescription()  .  "', user:'" . $_SESSION["user"]->getLogin() . "', iterationId: " . $iteration->getId();

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
