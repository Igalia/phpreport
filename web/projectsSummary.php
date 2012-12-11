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

    /* We check authentication and authorization */
    require_once(PHPREPORT_ROOT . '/web/auth.php');

    /* Include the generic header and sidebar*/
    define('PAGE_TITLE', "PhpReport - Projects Summary");
    include_once("include/header.php");
    include_once("include/sidebar.php");
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

?>

<script type="text/javascript" src="js/include/DateIntervalForm.js"></script>
<script type="text/javascript">

    Ext.onReady(function(){

        var dates = new Ext.ux.DateIntervalForm({
            renderTo: 'content',
            listeners: {
                'view': function (element, startDate, endDate) {
                    var params = {
                        init: startDate.getFullYear() + "-" +
                            (startDate.getMonth()+1) + "-" +
                            startDate.getDate(),
                        end: endDate.getFullYear() + "-" +
                            (endDate.getMonth()+1) + "-" +
                            endDate.getDate(),
                    };
                    customersGrid.store.baseParams = params;
                    usersGrid.store.baseParams = params;
                    customersGrid.store.load();
                    usersGrid.store.load();
                }
            }
        });

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
              stateId: 'projectCustomerGrid',
              loadMask: true,
              stripeRows: true,
              ds: new Ext.data.Store({
                    proxy: new Ext.data.HttpProxy({
                        url: this.storeUrl,
                        method: 'GET',
                    }),
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

        var customersGrid = new Ext.ux.DynamicGridPanel({
            id: 'CustomersGrid',
            storeUrl: 'services/getProjectCustomerReportJsonService.php',
            rowNumberer: false,
            checkboxSelModel: false,
            loadMask: true,
            columnLines: true,
            frame: true,
            title: 'Project - Customer Worked Hours Report',
            iconCls: 'silk-table',
            buttons: [{
                text: 'All data',
                handler: function () {
                    showAllColumns(customersGrid.getColumnModel());
                }
            },{
                text: 'Only totals',
                handler: function () {
                    hideAllColumnsExceptingTotals(customersGrid.getColumnModel());
                }
            }],
        });


            customersGrid.store.on('load', function(){
              /**
               * Thats the magic!
               *
               * JSON data returned from server has the column definitions
               */
              if(typeof(customersGrid.store.reader.jsonData.columns) === 'object') {
                var columns = [];
                var width = 0;

                  /**
                   * Adding RowNumberer or setting selection model as CheckboxSelectionModel
                   * We need to add them before other columns to display first
                   */
                if(customersGrid.rowNumberer) { columns.push(new Ext.grid.RowNumberer()); }
                if(customersGrid.checkboxSelModel) { columns.push(new Ext.grid.CheckboxSelectionModel()); }

                Ext.each(customersGrid.store.reader.jsonData.columns, function(column){
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
                customersGrid.getColumnModel().setConfig(columns);
                customersGrid.setSize(width, 500);

                summaryTabs.setSize(customersGrid.getColumnModel().getTotalWidth(), 500);

                if (!summaryTabs.rendered)
                    summaryTabs.render(Ext.get("content"));

                // We hide the dumb column
                customersGrid.getColumnModel().setHidden(customersGrid.getColumnModel().getIndexById('dumbColumn'), true);

              }

            }, this);

        var usersGrid = new Ext.ux.DynamicGridPanel({
            id: 'UsersGrid',
            storeUrl: 'services/getProjectUserReportJsonService.php',
            rowNumberer: false,
            checkboxSelModel: false,
            loadMask: true,
            columnLines: true,
            frame: true,
            title: 'Project - User Worked Hours Report',
            iconCls: 'silk-table',
            buttons: [{
                text: 'All data',
                handler: function () {
                    showAllColumns(usersGrid.getColumnModel());
                }
            },{
                text: 'Only totals',
                handler: function () {
                    hideAllColumnsExceptingTotals(usersGrid.getColumnModel());
                }
            }],
        });


            usersGrid.store.on('load', function(){
              /**
               * Thats the magic!
               *
               * JSON data returned from server has the column definitions
               */
              if(typeof(usersGrid.store.reader.jsonData.columns) === 'object') {
                var columns = [];
                var width = 0;

                  /**
                   * Adding RowNumberer or setting selection model as CheckboxSelectionModel
                   * We need to add them before other columns to display first
                   */
                if(usersGrid.rowNumberer) { columns.push(new Ext.grid.RowNumberer()); }
                if(usersGrid.checkboxSelModel) { columns.push(new Ext.grid.CheckboxSelectionModel()); }

                Ext.each(usersGrid.store.reader.jsonData.columns, function(column){
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
                usersGrid.getColumnModel().setConfig(columns);
                usersGrid.setSize(width, 500);

                // We hide the dumb column
                usersGrid.getColumnModel().setHidden(usersGrid.getColumnModel().getIndexById('dumbColumn'), true);

              }

            }, this);


        var summaryTabs = new Ext.TabPanel({
            activeTab: 0,
            frame: true,
            plain: true,
            items:[
                customersGrid,
                usersGrid
            ],
            listeners: { 'tabchange' : function(tabPanel, tab){
                    tabPanel.setSize(tab.getColumnModel().getTotalWidth() + 33, 500);
                }
            }
        });

        function showAllColumns(columnModel) {
            //getColumnCount returns the number of visible columns + 1
            for(var i = 0; i<columnModel.getColumnCount() - 1; i++) {
                columnModel.setHidden(i, false);
            }
        }

        function hideAllColumnsExceptingTotals(columnModel) {
            //getColumnCount returns the number of visible columns + 1
            //hide all except:
            //    column 0 = project
            //    getColumnCount() - 3 = total
            //    getColumnCount() - 2 = total %
            for(var i = 1; i<columnModel.getColumnCount() - 3; i++) {
                columnModel.setHidden(i, true);
            }
        }
    })

</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
