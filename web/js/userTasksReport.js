/*
 * Copyright (C) 2010-2015 Igalia, S.L. <info@igalia.com>
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

Ext.onReady(function () {
    var App = new Ext.App({});

    /* Schema of the information about projects */
    var projectRecord = new Ext.data.Record.create([
        {name:'id'},
        {name:'description'},
        {name:'customerName'}
    ]);

    /* Schema of the information about task-stories */
    var taskStoryRecord = new Ext.data.Record.create([
        {name:'id'},
        {name:'friendlyName'},
    ]);

    //schema of the information about users
    var userRecord = new Ext.data.Record.create([
        {name: 'id', type: 'int'},
        {name: "login", type: 'string'}
    ]);

    // store to load users
    var usersStore = new Ext.data.Store({
        id: 'usersStore',
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
        },
        proxy: new Ext.data.HttpProxy({
            method: 'GET',
            api: {
                read: {url: 'services/getAllUsersService.php'}
            },
        }),
        storeId: 'users',
        reader:new Ext.data.XmlReader({
            record: 'user',
            idProperty:'id'
        }, userRecord),
        remoteSort: false,
        sortInfo: {
            field: 'login',
            direction: 'ASC',
        },
        listeners: {
            'load': function () {
                /* Set the default value of the combobox to the logged in user on load */
                Ext.getCmp('userLogin').setValue(userId);
            }
        },
    });

    /* Store object for the projects */
    var projectsStore = new Ext.data.Store({
        parent: this,
        autoLoad: true,
        autoSave: false,
        baseParams: {
            'order': 'description',
        },
        filter: function(property, value, anyMatch, caseSensitive) {
            var fn;
            if (((property == 'description') || (property == 'customerName')) && !Ext.isEmpty(value, false)) {
                value = this.data.createValueMatcher(value, anyMatch, caseSensitive);
                fn = function(r){
                    return value.test(r.data['description']) || value.test(r.data['customerName']);
                };
            } else {
                fn = this.createFilterFn(property, value, anyMatch, caseSensitive);
            }
            return fn ? this.filterBy(fn) : this.clearFilter();
        },
        proxy: new Ext.data.HttpProxy({
            url: 'services/getProjectsAndCustomersForLoginService.php',
            method: 'GET'
        }),
        reader: new Ext.data.XmlReader(
            {record: 'project', id:'id'}, projectRecord),
        remoteSort: false,
        sortInfo: {
            field: 'description',
            direction: 'ASC',
        },
    });

    /* Store object for taskStory field */
    var taskStoryStore = new Ext.data.Store({
        autoLoad: true,
        autoSave: false,
        proxy: new Ext.data.HttpProxy({
            url: 'services/getOpenTaskStoriesService.php',
            method: 'GET'
        }),
        reader:new Ext.data.XmlReader(
            {record: 'taskStory', id:'id' }, taskStoryRecord),
        remoteSort: false,
    });

    /* Store object and data for task types */
    var taskTypeStore = new Ext.data.ArrayStore({
        idIndex: 0,
        fields: [
            'value',
            'displayText'
        ],
        data: [
            ['administration', 'Administration'],
            ['analysis', 'Analysis'],
            ['community', 'Community'],
            ['coordination', 'Coordination'],
            ['demonstration', 'Demonstration'],
            ['deployment', 'Deployment'],
            ['design', 'Design'],
            ['documentation', 'Documentation'],
            ['environment', 'Environment'],
            ['implementation', 'Implementation'],
            ['maintenance', 'Maintenance'],
            ['publication', 'Publication'],
            ['requirements', 'Requirements'],
            ['sales', 'Sales'],
            ['sys_maintenance', 'Systems maintenance'],
            ['teaching', 'Teaching'],
            ['technology', 'Technology'],
            ['test', 'Test'],
            ['training', 'Training'],
            ['traveling', 'Traveling'],
        ],
    });

    /* Renderer to show the project name in the grid */
    function projectRenderer(id) {
        var record =  projectsStore.getById(id);
        if (record) {
            return record.get('description');
        }
        return id;
    };

    /* Renderer to show the task story name in the grid */
    function taskStoryRenderer(id) {
        var record =  taskStoryStore.getById(id);
        if (record) {
            return record.get('friendlyName');
        }
        return id;
    };

    /* Renderer to show the task type in the grid */
    function taskTypeRenderer(value) {
        var record =  taskTypeStore.getById(value);
        if (record) {
            return record.get('displayText');
        }
        return value;
    };

    /* Panel containing all the search parameters */
    var filtersPanel = new Ext.FormPanel({
        labelWidth: 100,
        frame: true,
        width: 350,
        renderTo: 'content',
        defaults: {width: 230},
        items: [{
            fieldLabel: 'User',
            name: 'user',
            xtype: 'combo',
            allowBlank: false,
            autoLoad: true,
            typeAhead: true,
            mode: 'local',
            store: usersStore,
            valueField: 'id',
            displayField: 'login',
            triggerAction: 'all',
            forceSelection: true,
            id: 'userLogin'
        },{
            fieldLabel: 'Dates between',
            name: 'start',
            xtype: 'datefieldplus',
            format: 'd/m/Y',
            startDay: 1,
            useQuickTips: false,
            id: 'startDate',
            listeners: {
                'change': function (field, newValue, oldValue) {
                    if(!field.isValid()) return;
                    var date = field.parseDate(newValue);
                    Ext.getCmp('endDate').setMinValue(date);
                }
            }
        },{
            fieldLabel: 'and',
            name: 'end',
            xtype: 'datefieldplus',
            format: 'd/m/Y',
            startDay: 1,
            useQuickTips: false,
            id: 'endDate',
            listeners: {
                'change': function (field, newValue, oldValue) {
                    if(!field.isValid()) return;
                    var date = field.parseDate(newValue);
                    Ext.getCmp('startDate').setMaxValue(date);
                }
            }
        },{
            fieldLabel: 'Task description',
            name: 'filterText',
            xtype: 'combo',
            id: 'filterText',
            store: ['[empty]', '[not empty]'],
            triggerAction:'all',
            forceSelection: false,
            autoSelect: false,
        },{
            fieldLabel: 'Project',
            name: 'project',
            xtype: 'combo',
            id: 'project',
            store: projectsStore,
            mode: 'local',
            valueField: 'id',
            typeAhead: false,
            triggerAction: 'all',
            displayField: 'description',
            forceSelection: true,
            tpl: '<tpl for=".">' +
                    '<div class="x-combo-list-item" > <tpl>{description} </tpl>' +
                        '<tpl if="customerName">- {customerName}</tpl>' +
                    '</div>' +
                '</tpl>',
            listeners: {
                'select': function (combo, record, index) {
                    selectText = record.data['description'];

                    // We take customer name from the select combo, and injects its id to the taskRecord
                    if (record.data['customerName']) {
                        selectText = record.data['description'] + " - " + record.data['customerName'];
                    }

                    this.setValue(selectText);
                    combo.value = record.id;
                }
            }
        },{
            fieldLabel: 'Task type',
            name: 'type',
            xtype: 'combo',
            id: 'type',
            store: taskTypeStore,
            mode: 'local',
            valueField: 'value',
            displayField: 'displayText',
            typeAhead: true,
            triggerAction: 'all',
            forceSelection: true,
        },{
            fieldLabel: 'Story',
            name: 'filterStory',
            xtype: 'combo',
            id: 'filterStory',
            store: ['[empty]', '[not empty]'],
            triggerAction:'all',
            forceSelection: false,
            autoSelect: false,
        },{
            fieldLabel: 'TaskStory',
            name: 'taskStory',
            xtype: 'combo',
            id: 'taskStory',
            store: taskStoryStore,
            mode: 'local',
            valueField: 'id',
            displayField: 'friendlyName',
            typeAhead: true,
            triggerAction: 'all',
            forceSelection: true,
        },{
            fieldLabel: 'Telework',
            name: 'telework',
            xtype: 'combo',
            id: 'telework',
            mode: 'local',
            triggerAction:'all',
            store: ['yes', 'no'],
        },{
            fieldLabel: 'Onsite',
            name: 'onsite',
            xtype: 'combo',
            id: 'onsite',
            mode: 'local',
            triggerAction:'all',
            store: ['yes', 'no'],
        }],

        buttons: [{
            text: 'Find tasks',
            handler: findTasks,
        }],

        keys: [{
            key: [Ext.EventObject.ENTER],
            handler: findTasks,
        }],
    });
    /* Allow listing of user tasks of other users only for an admin user */
    if ( admin == "" ) {
        Ext.getCmp('userLogin').setDisabled(true);
    }

    /* Handler to invoke the search service */
    function findTasks () {
                if (Ext.getCmp('userLogin').getRawValue() == ""){
                    App.setAlert(false, "Check For Invalid Field Values");
                    return;
                }
                var baseParams = {
                    userId: Ext.getCmp('userLogin').getValue()
                };

                if (Ext.getCmp('startDate').getRawValue() != "") {
                    var date = Ext.getCmp('startDate').getValue();
                    baseParams.filterStartDate = date.getFullYear() + "-"
                        + (date.getMonth()+1) + "-" + date.getDate();
                }
                if (Ext.getCmp('endDate').getRawValue() != "") {
                    var date = Ext.getCmp('endDate').getValue();
                    baseParams.filterEndDate = date.getFullYear() + "-"
                        + (date.getMonth()+1) + "-" + date.getDate();
                }
                if (Ext.getCmp('filterText').getRawValue() != "") {
                    //this field is the selector for two different, incompatible
                    //parameters in the service
                    var value = Ext.getCmp('filterText').getValue();
                    if (value == '[empty]') {
                        baseParams.emptyText = true;
                    }
                    else if (value == '[not empty]') {
                        baseParams.emptyText = false;
                    }
                    else {
                        baseParams.filterText = value;
                    }
                }
                if (Ext.getCmp('project').getRawValue() != "") {
                    var value = Ext.getCmp('project').getValue();
                    baseParams.projectId = value;
                }
                if (Ext.getCmp('type').getRawValue() != "") {
                    var value = Ext.getCmp('type').getValue();
                    baseParams.type = value;
                }
                if (Ext.getCmp('filterStory').getRawValue() != "") {
                    //this field is the selector for two different, incompatible
                    //parameters in the service
                    var value = Ext.getCmp('filterStory').getValue();
                    if (value == '[empty]') {
                        baseParams.emptyStory = true;
                    }
                    else if (value == '[not empty]') {
                        baseParams.emptyStory = false;
                    }
                    else {
                        baseParams.filterStory = value;
                    }
                }
                if (Ext.getCmp('taskStory').getRawValue() != "") {
                    var value = Ext.getCmp('taskStory').getValue();
                    baseParams.taskStoryId = value;
                }
                if (Ext.getCmp('telework').getRawValue() != "") {
                    var value = Ext.getCmp('telework').getValue();
                    baseParams.telework = (value == 'yes')? true : false;
                }
                if (Ext.getCmp('onsite').getRawValue() != "") {
                    var value = Ext.getCmp('onsite').getValue();
                    baseParams.onsite = (value == 'yes')? true : false;
                }

                tasksStore.baseParams = baseParams;
                tasksStore.load();
    }

    /* Schema of the information about tasks */
    var taskRecord = new Ext.data.Record.create([
        {name:'id'},
        {name:'date'},
        {name:'initTime'},
        {name:'endTime'},
        {name: 'hours'},
        {name:'story'},
        {name:'telework'},
        {name:'onsite'},
        {name:'ttype'},
        {name:'text'},
        {name:'phase'},
        {name:'userId'},
        {name:'projectId'},
        {name:'taskStoryId'}
    ]);

    /* Proxy to the services related with load/save Projects */
    var proxy = new Ext.data.HttpProxy({
        api: {
            read: {url: 'services/getTasksFiltered.php', method: 'GET'},
        },
    });

    /* Store object for the tasks */
    var tasksStore = new Ext.data.Store({
        id: 'tasksStore',
        autoLoad: false,
        autoSave: false,
        storeId: 'tasks',
        proxy: proxy,
        reader: new Ext.data.XmlReader(
                {record: 'task', idProperty:'id' }, taskRecord),
        remoteSort: false,
        sortInfo: {
            field: 'date',
            direction: 'ASC',
        },
    });

    var columnModel = new Ext.grid.ColumnModel([
        {
            header: 'Date',
            xtype: 'datecolumn',
            format: 'd/m/Y',
            sortable: true,
            dataIndex: 'date',
        },{
            header: 'Init time',
            sortable: true,
            dataIndex: 'initTime',
        },{
            header: 'End time',
            sortable: true,
            dataIndex: 'endTime',
        },{
            header: 'Hours',
            sortable: true,
            dataIndex: 'hours',
        }, {
            header: "Project",
            sortable: true,
            dataIndex: 'projectId',
            renderer: projectRenderer,
        },{
            header: "Task type",
            sortable: true,
            dataIndex: 'ttype',
            renderer: taskTypeRenderer,
        },{
            header: 'Telework',
            sortable: true,
            dataIndex: 'telework',
            xtype: 'booleancolumn',
            trueText: "<span style='color:green;'>Yes</span>",
            falseText: "<span style='color:red;'>No</span>",
        },{
            header: 'Onsite',
            sortable: true,
            dataIndex: 'onsite',
            xtype: 'booleancolumn',
            trueText: "<span style='color:green;'>Yes</span>",
            falseText: "<span style='color:red;'>No</span>",
        },{
            header: 'Story',
            sortable: true,
            dataIndex: 'story',
        },{
            header: "Task story",
            sortable: true,
            dataIndex: 'taskStoryId',
            renderer: taskStoryRenderer,
        },{
            header: 'Description',
            sortable: true,
            dataIndex: 'text',
        }
    ]);

    // setup the panel for the grid of tasks
    var tasksGrid = new Ext.ux.ExportableGridPanel({
        id: 'tasksGrid',
        renderTo: 'content',
        frame: true,
        height: 500,
        width: '100%',
        iconCls: 'silk-book',
        store: tasksStore,
        frame: true,
        title: 'Tasks',
        style: 'margin-top: 10px',
        renderTo: 'content',
        loadMask: true,
        stripeRows: true,
        colModel: columnModel,
        columnLines: true,
        buttons: [{
            text: 'Standard view',
            handler: showStandardView,
        },{
            text: 'Extended view',
            handler: showExtendedView,
        }],
    });

    //function to show only a subset of columns and hide the others
    function showStandardView() {
        columnModel.setHidden(0, false);  //date
        columnModel.setHidden(1, true);  //init
        columnModel.setHidden(2, true);  //end
        columnModel.setHidden(3, false);  //hours
        columnModel.setHidden(4, false);  //project
        columnModel.setHidden(5, false); //task type
        columnModel.setHidden(6, true);   //telework
        columnModel.setHidden(7, true);   //onsite
        columnModel.setHidden(8, true);   //story
        columnModel.setHidden(9, false);  //taskStory
        columnModel.setHidden(10, false);  //description
        columnModel.setColumnWidth(0, 80);
        columnModel.setColumnWidth(1, 55);
        columnModel.setColumnWidth(2, 55);
        columnModel.setColumnWidth(3, 55);
        columnModel.setColumnWidth(4, 200);
        columnModel.setColumnWidth(5, 120);
        columnModel.setColumnWidth(9, 120);
        columnModel.setColumnWidth(10, 435);
    }

    //function to show all the columns
    function showExtendedView() {
        columnModel.setHidden(0, false);  //date
        columnModel.setHidden(1, false);  //init
        columnModel.setHidden(2, false);  //end
        columnModel.setHidden(3, false);  //hours
        columnModel.setHidden(4, false);  //project
        columnModel.setHidden(5, false); //task type
        columnModel.setHidden(6, false);   //telework
        columnModel.setHidden(7, false);   //onsite
        columnModel.setHidden(8, false);   //story
        columnModel.setHidden(9, false);  //taskStory
        columnModel.setHidden(10, false);  //description
        columnModel.setColumnWidth(0, 80);
        columnModel.setColumnWidth(1, 55);
        columnModel.setColumnWidth(2, 55);
        columnModel.setColumnWidth(3, 55);
        columnModel.setColumnWidth(4, 200);
        columnModel.setColumnWidth(5, 100);
        columnModel.setColumnWidth(6, 80);
        columnModel.setColumnWidth(7, 50);
        columnModel.setColumnWidth(8, 50);
        columnModel.setColumnWidth(9, 100);
        columnModel.setColumnWidth(10, 435);
    }

    //hide the advanced columns
    showStandardView();
});
