/*
 * Copyright (C) 2009-2019 Igalia, S.L. <info@igalia.com>
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
                        value: projectData.description,
                    },{
                        id:'id',
                        name: 'id',
                        fieldLabel:'Id',
                        value: projectData.id,
                    },{
                        id:'customer',
                        name: 'customer',
                        fieldLabel:'Customer',
                        value: projectData.customerName,
                    },{
                        id:'init',
                        name: 'init',
                        fieldLabel:'Init Date',
                        value: (projectData.initDate)? projectData.initDate.format('d/m/Y') : "---",
                    },{
                        id:'end',
                        name: 'end',
                        fieldLabel:'End Date',
                        value: (projectData.endDate)? projectData.endDate.format('d/m/Y') : "---",
                    },{
                        id:'active',
                        name:'active',
                        fieldLabel: 'Active',
                        value: projectData.active? "Yes":"No",
                        //check if project is active but finish date has passed
                        style: (projectData.active && projectData.endDate && new Date() > projectData.endDate)?
                               {color:"red"} : {},
                    },{
                        id: 'estHours',
                        name:'estHours',
                        fieldLabel: 'Estimated Hours',
                        value: projectData.estimatedHours,
                    },{
                        id: 'movedHours',
                        name:'movedHours',
                        fieldLabel: 'Moved Hours',
                        value: projectData.movedHours,
                    },{
                        id: 'invoice',
                        name:'invoice',
                        fieldLabel: 'Invoice',
                        value: projectData.invoice,
                    },{
                        id: 'Type',
                        name:'Type',
                        fieldLabel: 'Type',
                        value: projectData.type,
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
                        value: projectData.finalEstimatedHours,
                    },{
                        id: 'workedHours',
                        name:'workedHours',
                        fieldLabel: 'Worked Hours',
                        value: projectData.workedHours,
                    },{
                        id: 'workDeviation',
                        name:'workDeviation',
                        fieldLabel: 'Deviation',
                        value: projectData.workDeviation,
                    },{
                        id: 'workDeviationPercent',
                        name:'workDeviationPercent',
                        fieldLabel: 'Deviation %',
                        value: projectData.workDeviationPercent,
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
                        value: projectData.estInvoice,
                    },{
                        id: 'currentInvoice',
                        name:'currentInvoice',
                        fieldLabel: 'Current Price',
                        value: projectData.currentInvoice,
                    },{
                        id: 'invoiceDeviation',
                        name:'invoiceDeviation',
                        fieldLabel: 'Deviation',
                        value: projectData.invoiceDeviation,
                    },{
                        id: 'invoiceDeviationPercent',
                        name:'invoiceDeviationPercent',
                        fieldLabel: 'Deviation %',
                        value: projectData.invoiceDeviationPercent,
                    }
                ]
            }]
    });

    mainPanel.render(Ext.get("content"));

        Ext.ux.DynamicGridPanel = Ext.extend(Ext.ux.ExportableGridPanel, {

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
            storeUrl: 'services/getProjectUserCustomerReportJsonService.php?pid=' + projectData.id,
            rowNumberer: false,
            checkboxSelModel: false,
            width: 400,
            height: 250,
            columnLines: true,
            frame: false,
            title: 'Project Users Worked Hours Report',
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
            storeUrl: 'services/getProjectUserStoryReportJsonService.php?pid=' + projectData.id,
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

        var grid3 = new Ext.ux.DynamicGridPanel({
            id: 'projectUserWeeklyHoursGrid',
            stateId: 'projectUserWeeklyHoursGrid',
            storeUrl: 'services/getProjectUserWeeklyHoursReportJsonService.php?pid=' + projectData.id,
            rowNumberer: false,
            columnLines: true,
            checkboxSelModel: false,
            width: 400,
            height: 250,
            frame: false,
            title: 'Project User-Weekly Worked Hours Report',
            iconCls: 'silk-table',
        });


        grid3.store.on('load', function(){

            /**
             * Thats the magic!
             *
             * JSON data returned from server has the column definitions
             */
            if(typeof(grid3.store.reader.jsonData.columns) === 'object') {

                var columns = [];
                var width = 0;

                /**
                 * Adding RowNumberer or setting selection model as CheckboxSelectionModel
                 * We need to add them before other columns to display first
                 */
                if(grid3.rowNumberer) { columns.push(new Ext.grid3.RowNumberer()); }
                if(grid3.checkboxSelModel) { columns.push(new Ext.grid3.CheckboxSelectionModel()); }

                Ext.each(grid3.store.reader.jsonData.columns, function(column){
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
                grid3.getColumnModel().setConfig(columns);
                grid3.setSize(width, 250);

                if (!grid3.rendered)
                    grid3.render(Ext.get("content"));

                // We hide the dumb column
                grid3.getColumnModel().setHidden(grid3.getColumnModel().getIndexById('dumbColumn'), true);

            }

        }, this);

    // dates filter form
    var workingResultsForm = new Ext.ux.DateIntervalForm({
        renderTo: 'content',
        listeners: {
            'view': function (element, init, end) {

                grid.store.removeAll();
                grid.store.proxy.conn.url= 'services/getProjectUserCustomerReportJsonService.php' +
                    '?pid=' + projectData.id +
                    '&init=' + init.getFullYear() + "-" + (init.getMonth()+1) + "-" + init.getDate() +
                    "&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();
                grid.store.load();

                grid2.store.removeAll();
                grid2.store.proxy.conn.url= 'services/getProjectUserStoryReportJsonService.php' +
                    '?pid=' + projectData.id +
                    '&init=' + init.getFullYear() + "-" + (init.getMonth()+1) + "-" + init.getDate() +
                    "&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();
                grid2.store.load();

                grid3.store.removeAll();
                grid3.store.proxy.conn.url= 'services/getProjectUserWeeklyHoursReportJsonService.php' +
                    '?pid=' + projectData.id +
                    '&init=' + init.getFullYear() + "-" + (init.getMonth()+1) + "-" + init.getDate() +
                    "&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();
                grid3.store.load();

            }
        }
    });
    workingResultsForm.focus();

    grid.store.load();
    grid2.store.load();
    grid3.store.load();

});
