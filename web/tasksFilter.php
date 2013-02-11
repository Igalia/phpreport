<?php
/*
 * Copyright (C) 2010-2012 Igalia, S.L. <info@igalia.com>
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

$sid = $_GET["sid"];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - User tasks report");
include_once("include/header.php");
include_once("include/sidebar.php");

?>

<script type="text/javascript">

Ext.onReady(function () {

    <?php if ($sid) {?>

    var sessionId = <?php echo $sid;?>;

    <?php } ?>

    var userId = <?php echo $_SESSION['user']->getId()?>;

    /* Schema of the information about customers */
    var customerRecord = new Ext.data.Record.create([
        {name:'id'},
        {name:'name'},
    ]);

    /* Schema of the information about projects */
    var projectRecord = new Ext.data.Record.create([
        {name:'id'},
        {name:'description'},
    ]);

    /* Schema of the information about task-stories */
    var taskStoryRecord = new Ext.data.Record.create([
        {name:'id'},
        {name:'friendlyName'},
    ]);

    /* Store object for the projects */
    var projectsStore = new Ext.data.Store({
        parent: this,
        autoLoad: true,
        autoSave: false,
        baseParams: {
            'order': 'description',
        },
        proxy: new Ext.data.HttpProxy({
            url: 'services/getCustomerProjectsService.php',
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

    /* Store object for customers */
    var customersStore = new Ext.data.Store({
        autoLoad: true,
        autoSave: false,
        baseParams: {
            'order': 'name',
        },
        proxy: new Ext.data.HttpProxy({
            url: 'services/getUserCustomersService.php',
            method: 'GET'
        }),
        reader: new Ext.data.XmlReader(
            {record: 'customer', id:'id' }, customerRecord),
        remoteSort: false,
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

    /* Renderer to show the customer name in the grid */
    function customerRenderer(id) {
        var record =  customersStore.getById(id);
        if (record) {
            return record.get('name');
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

    /* Renderer to show the customer name in the grid */
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
            fieldLabel: 'Dates between',
            name: 'start',
            xtype: 'datefield',
            format: 'd/m/Y',
            startDay: 1,
            id: 'startDate',
            vtype:'daterange',
            endDateField: 'endDate' // id of the end date field
        },{
            fieldLabel: 'and',
            name: 'end',
            xtype: 'datefield',
            format: 'd/m/Y',
            startDay: 1,
            id: 'endDate',
            vtype:'daterange',
            startDateField: 'startDate' // id of the start date field
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
            fieldLabel: 'Customer',
            name: 'customer',
            xtype: 'combo',
            id: 'customer',
            store: customersStore,
            mode: 'local',
            valueField: 'id',
            typeAhead: true,
            triggerAction: 'all',
            displayField: 'name',
            forceSelection: true,
        },{
            fieldLabel: 'Project',
            name: 'project',
            xtype: 'combo',
            id: 'project',
            store: projectsStore,
            mode: 'local',
            valueField: 'id',
            typeAhead: true,
            triggerAction: 'all',
            displayField: 'description',
            forceSelection: true,
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

    /* Handler to invoke the search service */
    function findTasks () {
                var baseParams = {
                    'userId': userId,
                    <?php if ($sid) {?>
                        'sid': sessionId,
                    <?php } ?>
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
                if (Ext.getCmp('customer').getRawValue() != "") {
                    var value = Ext.getCmp('customer').getValue();
                    baseParams.customerId = value;
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
        {name:'story'},
        {name:'telework'},
        {name:'onsite'},
        {name:'ttype'},
        {name:'text'},
        {name:'phase'},
        {name:'userId'},
        {name:'projectId'},
        {name:'customerId'},
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
            header: "Customer",
            sortable: true,
            dataIndex: 'customerId',
            renderer: customerRenderer,
        },{
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
    var tasksGrid = new Ext.grid.GridPanel({
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
        columnModel.setHidden(1, false);  //init
        columnModel.setHidden(2, false);  //end
        columnModel.setHidden(3, true);   //customer
        columnModel.setHidden(4, false);  //project
        columnModel.setHidden(5, true);   //task type
        columnModel.setHidden(6, true);   //telework
        columnModel.setHidden(7, true);   //onsite
        columnModel.setHidden(8, false);  //story
        columnModel.setHidden(9, false);  //taskStory
        columnModel.setHidden(10, false);  //description
        columnModel.setColumnWidth(0, 80);
        columnModel.setColumnWidth(1, 55);
        columnModel.setColumnWidth(2, 55);
        columnModel.setColumnWidth(4, 120);
        columnModel.setColumnWidth(8, 120);
        columnModel.setColumnWidth(9, 100);
        columnModel.setColumnWidth(10, 435);
    }

    //function to show all the columns
    function showExtendedView() {
        columnModel.setHidden(0, false);  //date
        columnModel.setHidden(1, false);  //init
        columnModel.setHidden(2, false);  //end
        columnModel.setHidden(3, false);  //customer
        columnModel.setHidden(4, false);  //project
        columnModel.setHidden(5, false);  //task type
        columnModel.setHidden(6, false);  //telework
        columnModel.setHidden(7, false);  //onsite
        columnModel.setHidden(8, false);  //story
        columnModel.setHidden(9, false);  //taskStory
        columnModel.setHidden(10, false);  //description
        columnModel.setColumnWidth(0, 80);
        columnModel.setColumnWidth(1, 55);
        columnModel.setColumnWidth(2, 55);
        columnModel.setColumnWidth(3, 90);
        columnModel.setColumnWidth(4, 100);
        columnModel.setColumnWidth(5, 80);
        columnModel.setColumnWidth(6, 50);
        columnModel.setColumnWidth(7, 50);
        columnModel.setColumnWidth(8, 100);
        columnModel.setColumnWidth(9, 100);
        columnModel.setColumnWidth(10, 205);
    }

    //hide the advanced columns
    showStandardView();
});
</script>

<div id="content">
</div>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
