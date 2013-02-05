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

    $pid = $_GET['pid'];

    /* We check authentication and authorization */
    require_once(PHPREPORT_ROOT . '/web/auth.php');

    /* Include the generic header and sidebar*/
    define('PAGE_TITLE', "PhpReport - Project Details");
    include_once("include/header.php");
    include_once("include/sidebar.php");
    include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/StoryVO.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

    $project = ProjectsFacade::GetCustomProject($pid);


?>

<script type="text/javascript" src="js/include/DateIntervalForm.js"></script>
<script type="text/javascript">

    Ext.onReady(function(){


    // Main Panel
    var mainPanel = new Ext.FormPanel({
        width: 426,
        labelWidth: 125,
            frame:true,
            layout: 'table',
            layoutConfig:{
                columns: 2,
                tableAttrs: {
                    style: {
                        width: '100%'
                    }
                }
            },
            title: 'Project Data',
            bodyStyle: 'padding:5px 5px 0px 5px;',
            defaults: {
                 colspan: 2,
            },
            defaultType: 'fieldset',
            items:[{
                xtype: 'fieldset',
                autoWidth: true,
                columnWidth: 0.5,
                title: 'Basic Data',
                collapsible: true,
                autoHeight: true,
                defaults: {
                     width: 250,
                     labelStyle: 'width: 125; font-weight:bold; padding: 0 0 0 0;',
                },
                defaultType:'displayfield',
                items: [{
                        id:'name',
                        name: 'name',
                        fieldLabel:'Name',
                        <?php
                              echo "value:'" . $project->getDescription() . "'";
                        ?>
                    },{
                        id:'id',
                        name: 'id',
                        fieldLabel:'Id',
                        <?php
                              echo "value:'" . $project->getId() . "'";
                        ?>
                    },{
                        id:'init',
                        name: 'init',
                        fieldLabel:'Init Date',
                        <?php
                              echo "value:'" . ((is_null($project->getInit()))?(' --- '):($project->getInit()->format('d/m/Y'))) . "'";
                        ?>
                    },{
                        id:'end',
                        name: 'end',
                        fieldLabel:'End Date',
                        <?php
                              echo "value:'" . ((is_null($project->getEnd()))?(' --- '):($project->getEnd()->format('d/m/Y'))) . "'";
                        ?>
                    },{
                        id:'active',
                        name:'active',
                        fieldLabel: 'Active',
                        <?php

                            $active = $project->getActivation();
                            if (isset($active))
                            {
                                if ($active == False)
                                    echo "value:'No'";
                                else {
                                    echo "value:'Yes'";
                                    if(!is_null($project->getEnd()) &&
                                            time() > $project->getEnd()->getTimestamp())
                                        //project is open but finish date has passed
                                        echo ',style: {color:"red"}';
                                }
                            }

                        ?>
                    },{
                        id: 'estHours',
                        name:'estHours',
                        fieldLabel: 'Estimated Hours',
                        <?php

                            echo "value:'" . $project->getEstHours() . "'";

                        ?>
                    },{
                        id: 'movedHours',
                        name:'movedHours',
                        fieldLabel: 'Moved Hours',
                        <?php

                            echo "value:'" . $project->getMovedHours() . "'";

                        ?>
                    },{
                        id: 'invoice',
                        name:'invoice',
                        fieldLabel: 'Invoice',
                        <?php

                            echo "value:'" . $project->getInvoice() . "'";

                        ?>
                    },{
                        id: 'Type',
                        name:'Type',
                        fieldLabel: 'Type',
                        <?php

                            echo "value:'" . $project->getType() . "'";

                        ?>
                    }
                ]
            },{
                xtype: 'fieldset',
                columnWidth: 0.5,
                title: 'Work Hours Data',
                width: 200,
                collapsible: true,
                colspan: 1,
                autoHeight: true,
                defaults: {
                     width: 50,
                     labelStyle: 'width: 125; font-weight:bold; padding: 0 0 0 0;',
                },
                defaultType:'displayfield',
                items: [{
                      id: 'estimatedHoursWithMoved',
                        name:'estimatedHoursWithMoved',
                        fieldLabel: 'Estimated Hours',
                        <?php

                            echo "value:'" . $project->getFinalEstHours() . "'";

                        ?>
                    },{
                      id: 'workedHours',
                        name:'workedHours',
                        fieldLabel: 'Worked Hours',
                        <?php

                            echo "value:'" . $project->getWorkedHours() . "'";

                        ?>
                    },{
                        id: 'workDeviation',
                        name:'workDeviation',
                        fieldLabel: 'Deviation',
                        <?php

                            echo "value:'" . round($project->getAbsDev(), 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    },{
                        id: 'workDeviationPercent',
                        name:'workDeviationPercent',
                        fieldLabel: 'Deviation %',
                        <?php

                            echo "value:'" . round($project->getPercDev(), 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    }
                ]
            },{
                xtype: 'fieldset',
                columnWidth: 0.5,
                width: 200,
                title: 'Price Per Hour Data',
                collapsible: true,
                colspan:1,
                autoHeight: true,
                defaults: {
                     width: 50,
                     labelStyle: 'width: 125; font-weight:bold; padding: 0 0 0 0;',
                },
                defaultType:'displayfield',
                items: [{
                        id: 'estInvoice',
                        name:'estInvoice',
                        fieldLabel: 'Estimated Price',
                        <?php

                            echo "value:'" . round($project->getEstHourInvoice(), 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    },{
                        id: 'currentInvoice',
                        name:'currentInvoice',
                        fieldLabel: 'Current Price',
                        <?php

                            echo "value:'" . round($project->getWorkedHourInvoice(), 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    },{
                        id: 'invoiceDeviation',
                        name:'invoiceDeviation',
                        fieldLabel: 'Deviation',
                        <?php

                            echo "value:'" . round($project->getWorkedHourInvoiceAbsoluteDeviation(), 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    },{
                        id: 'invoiceDeviationPercent',
                        name:'invoiceDeviationPercent',
                        fieldLabel: 'Deviation %',
                        <?php

                            echo "value:'" . round($project->getWorkedHourInvoiceRelativeDeviation(), 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    }
                ]
            }]
    });

    mainPanel.render(Ext.get("content"));

        Ext.ux.DynamicGridPanel = Ext.extend(Ext.grid.GridPanel, {

          initComponent: function(){
            /**
             * Default configuration options.
             *
             * You are free to change the values or add/remove options.
             * The important point is to define a data store with JsonReader
             * without configuration and columns with empty array. We are going
             * to setup our reader with the metaData information returned by the server.
             * See http://extjs.com/deploy/dev/docs/?class=Ext.data.JsonReader for more
             * information how to configure your JsonReader with metaData.
             *
             * A data store with remoteSort = true displays strange behaviours such as
             * not to display arrows when you sort the data and inconsistent ASC, DESC option.
             * Any suggestions are welcome
             */
            var config = {
              viewConfig: {forceFit: true},
              stateful: true,
              loadMask: true,
              stripeRows: true,
              ds: new Ext.data.Store({
                    url: this.storeUrl,
                    reader: new Ext.data.JsonReader()
              }),
              columns: []
            };

            Ext.apply(this, config);
            Ext.apply(this.initialConfig, config);

            Ext.ux.DynamicGridPanel.superclass.initComponent.apply(this, arguments);
          },

          onRender: function(ct, position){
            this.colModel.defaultSortable = true;

            Ext.ux.DynamicGridPanel.superclass.onRender.call(this, ct, position);

          }
        });

        var grid = new Ext.ux.DynamicGridPanel({
            id: 'projectUserCustomerGrid',
            stateId: 'projectUserCustomerGrid',
            storeUrl: 'services/getProjectUserCustomerReportJsonService.php?<?php

                echo "login=" . $login;

                if ($sid!="")
                    echo "&sid=" . $sid;

                echo "&pid=" . $pid;?>',
            rowNumberer: false,
            checkboxSelModel: false,
            width: 400,
            height: 250,
            columnLines: true,
            frame: false,
            title: 'Project User-Customer Worked Hours Report',
            iconCls: 'silk-table',
        });


            grid.store.on('load', function(){
              /**
               * Thats the magic!
               *
               * JSON data returned from server has the column definitions
               */
              if(typeof(grid.store.reader.jsonData.columns) === 'object') {
                var columns = [];
                var width = 0;

                  /**
                   * Adding RowNumberer or setting selection model as CheckboxSelectionModel
                   * We need to add them before other columns to display first
                   */
                if(grid.rowNumberer) { columns.push(new Ext.grid.RowNumberer()); }
                if(grid.checkboxSelModel) { columns.push(new Ext.grid.CheckboxSelectionModel()); }

                Ext.each(grid.store.reader.jsonData.columns, function(column){
                  columns.push(column);
                  width += column.width;
                });

                // We add a dumb column we'll hide for preventing rendering
                // problems on resizing
                columns.push( new Ext.grid.Column({dataIndex: 'dumb', header: 'dumb', id: 'dumbColumn', width: 25}));

                width += 25;

              /**
               * Setting column model configuration
               */
                grid.getColumnModel().setConfig(columns);
                grid.setSize(width, 250);

                if (!grid.rendered)
                    grid.render(Ext.get("content"));

                // We hide the dumb column
                grid.getColumnModel().setHidden(grid.getColumnModel().getIndexById('dumbColumn'), true);

              }

            }, this);

        var grid2 = new Ext.ux.DynamicGridPanel({
            id: 'projectUserStoryGrid',
            stateId: 'projectUserStoryGrid',
            storeUrl: 'services/getProjectUserStoryReportJsonService.php?<?php

                echo "login=" . $login;

                if ($sid!="")
                    echo "&sid=" . $sid;

                echo "&pid=" . $pid;?>',
            rowNumberer: false,
            columnLines: true,
            checkboxSelModel: false,
            width: 400,
            height: 250,
            frame: false,
            title: 'Project User-Story Worked Hours Report',
            iconCls: 'silk-table',
        });


            grid2.store.on('load', function(){
              /**
               * Thats the magic!
               *
               * JSON data returned from server has the column definitions
               */
              if(typeof(grid2.store.reader.jsonData.columns) === 'object') {
                var columns = [];
                var width = 0;

                  /**
                   * Adding RowNumberer or setting selection model as CheckboxSelectionModel
                   * We need to add them before other columns to display first
                   */
                if(grid2.rowNumberer) { columns.push(new Ext.grid2.RowNumberer()); }
                if(grid2.checkboxSelModel) { columns.push(new Ext.grid2.CheckboxSelectionModel()); }

                Ext.each(grid2.store.reader.jsonData.columns, function(column){
                  columns.push(column);
                  width += column.width;
                });

                // We add a dumb column we'll hide for preventing rendering
                // problems on resizing
                columns.push( new Ext.grid.Column({dataIndex: 'dumb', header: 'dumb', id: 'dumbColumn', width: 25}));

                width += 25;

              /**
               * Setting column model configuration
               */
                grid2.getColumnModel().setConfig(columns);
                grid2.setSize(width, 250);

                if (!grid2.rendered)
                    grid2.render(Ext.get("content"));

                // We hide the dumb column
                grid2.getColumnModel().setHidden(grid2.getColumnModel().getIndexById('dumbColumn'), true);

              }

            }, this);

    // dates filter form
    var workingResultsForm = new Ext.ux.DateIntervalForm({
        renderTo: 'content',
        listeners: {
            'view': function (element, init, end) {

                grid.store.removeAll();

                grid.store.proxy.conn.url= 'services/getProjectUserCustomerReportJsonService.php?<?php

                    echo "login=" . $login;

                    if ($sid!="")
                        echo "&sid=" . $sid;

                                        echo "&pid=" . $pid;

                ?>&init=' + init.getFullYear() + "-" + (init.getMonth()+1) + "-" + init.getDate()  + "&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();


                grid.store.load();


                grid2.store.removeAll();

                grid2.store.proxy.conn.url= 'services/getProjectUserStoryReportJsonService.php?<?php

                    echo "login=" . $login;

                    if ($sid!="")
                        echo "&sid=" . $sid;

                                        echo "&pid=" . $pid;

                ?>&init=' + init.getFullYear() + "-" + (init.getMonth()+1) + "-" + init.getDate()  + "&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();

                grid2.store.load();

            }
        }
    });

    grid.store.load();
    grid2.store.load();

    })

</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
