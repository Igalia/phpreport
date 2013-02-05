<?php
/*
 * Copyright (C) 2009-2012 Igalia, S.L. <info@igalia.com>
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

    /* Include the generic header and sidebar*/
    define('PAGE_TITLE', "PhpReport - Users Evaluation");
    include_once("include/header.php");
    include_once("include/sidebar.php");
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

?>

<script type="text/javascript" src="js/include/DateIntervalForm.js"></script>
<script type="text/javascript">

    Ext.onReady(function(){

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
              stateful: true,
              stateId: 'userStoryGrid',
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

        // dates filter form
        var userEvaluationResultsForm = new Ext.ux.DateIntervalForm({
            renderTo: 'content',
            listeners: {
                'view': function (element, init, end) {

                    storiesGrid.store.removeAll();

                    storiesGrid.store.proxy.conn.url= 'services/getUserStoryReportJsonService.php?<?php

                        if ($sid!="")
                            echo "&sid=" . $sid . "&";

                    ?>init=' + init.getFullYear() + "-" + (init.getMonth()+1) + "-" + init.getDate()  + "&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();


                    storiesGrid.store.load();

                }
            }
        });


        var storiesGrid = new Ext.ux.DynamicGridPanel({
            id: 'StoriesGrid',
            storeUrl: 'services/getUserStoryReportJsonService.php?<?php

                if ($sid!="")
                    echo "&sid=" . $sid;?>',
            rowNumberer: false,
            checkboxSelModel: false,
            loadMask: true,
            columnLines: true,
            frame: true,
            title: 'User - Story Worked Hours Report',
            iconCls: 'silk-table',
        });


            storiesGrid.store.on('load', function(){
              /**
               * Thats the magic!
               *
               * JSON data returned from server has the column definitions
               */
              if(typeof(storiesGrid.store.reader.jsonData.columns) === 'object') {
                var columns = [];

                  /**
                   * Adding RowNumberer or setting selection model as CheckboxSelectionModel
                   * We need to add them before other columns to display first
                   */
                if(storiesGrid.rowNumberer) { columns.push(new Ext.grid.RowNumberer()); }
                if(storiesGrid.checkboxSelModel) { columns.push(new Ext.grid.CheckboxSelectionModel()); }

                Ext.each(storiesGrid.store.reader.jsonData.columns, function(column){
                  columns.push(column);
                });

                // We add a dumb column we'll hide for preventing rendering
                // problems on resizing
                columns.push( new Ext.grid.Column({dataIndex: 'dumb', header: 'dumb', id: 'dumbColumn', width: 10}));

              /**
               * Setting column model configuration
               */
                storiesGrid.getColumnModel().setConfig(columns);

                storiesGrid.setSize(storiesGrid.getColumnModel().getTotalWidth(), 500);

                summaryTabs.setSize(storiesGrid.getColumnModel().getTotalWidth(), 500);

                if (!summaryTabs.rendered)
                    summaryTabs.render(Ext.get("content"));

                // We hide the dumb column
                storiesGrid.getColumnModel().setHidden(storiesGrid.getColumnModel().getIndexById('dumbColumn'), true);

              }

            }, this);

        var summaryTabs = new Ext.TabPanel({
            activeTab: 0,
            frame: true,
            plain: true,
            items:[
                storiesGrid,
            ],
            listeners: { 'tabchange' : function(tabPanel, tab){
                    tabPanel.setSize(tab.getColumnModel().getTotalWidth() + 33, 500);
                }
            }
        });

    })

</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
