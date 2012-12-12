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

$sid = $_GET["sid"];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Projects Evaluation");
include_once("include/header.php");
include_once("include/sidebar.php");
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
include_once(PHPREPORT_ROOT . '/util/UnknownParameterException.php');
include_once(PHPREPORT_ROOT . '/util/LoginManager.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/AdminFacade.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

// We retrieve the Areas
$areas = AdminFacade::GetAllAreas();

?>

<script type="text/javascript">

Ext.onReady(function(){

    <?php if ($sid) {?>

    var sessionId = <?php echo $sid;?>;

    <?php } ?>

    var areasStore = new Ext.data.ArrayStore({
        id: 0,
        fields: ['id', 'name'],
        data : [
        <?php

        foreach((array)$areas as $area)
            echo "[{$area->getId()}, '{$area->getName()}'],";

    ?>]});

    function areas(val){

        var record =  areasStore.getById(val);

        if (record)
            return record.get('name');
        else
            return val;

    };

    function deviation(val){

        if(val > 0)
            return '<span style="color:red;">' + Ext.util.Format.number(val, '0,000.00') + '</span>';
        else return '<span style="color:green;">' + Ext.util.Format.number(val, '0,000.00') + '</span>';

    }

    function relativeDeviation(val){

        if(val > 0)
            return '<span style="color:red;">' + Ext.util.Format.number(val, '0,000.00') + ' % </span>';
        else if (val == '0')
            return '<span style="color:green;">' + Ext.util.Format.number(val, '0,000.00') + ' % </span>';
        else if (val == '')
             return '-----';
        else return '<span style="color:green;">' + Ext.util.Format.number(val, '0,000.00') + ' % </span>';

    }

    function profit(val){

        if(val > 0)
            return '<span style="color:green;">' + Ext.util.Format.number(val, '0,000.00') + '</span>';
        else return '<span style="color:red;">' + Ext.util.Format.number(val, '0,000.00') + '</span>';

    }

    function loadProjects() {
        var baseParams = {
            <?php if ($sid) {?>
                'sid': sessionId,
            <?php } ?>
        };
        if (Ext.getCmp('name').getRawValue() != "") {
            baseParams.description = Ext.getCmp('name').getValue();
        }
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
        if (Ext.getCmp('activation').getRawValue() != "") {
            var value = Ext.getCmp('activation').getValue();
            baseParams.activation = (value == 'yes')? true : false;
        }
        if (Ext.getCmp('area').getRawValue() != "") {
            baseParams.areaId = Ext.getCmp('area').getValue();
        }
        if (Ext.getCmp('type').getRawValue() != "") {
            baseParams.type = Ext.getCmp('type').getValue();
        }

        projectsStore.baseParams = baseParams;
        projectsStore.load();

    };

    var filtersPanel = new Ext.FormPanel({
        labelWidth: 100,
        frame: true,
        width: 350,
        renderTo: 'content',
        defaults: {width: 230},
        items: [{
            fieldLabel: 'Project name',
            name: 'name',
            xtype: 'textfield',
            id: 'name',
        },{
            fieldLabel: 'Activation',
            name: 'activation',
            xtype: 'combo',
            id: 'activation',
            mode: 'local',
            valueField: 'value',
            displayField: 'displayText',
            triggerAction:'all',
            store: new Ext.data.ArrayStore({
                fields: [
                    'value',
                    'displayText'
                ],
                data: [
                    ['yes', 'yes'],
                    ['no', 'no'],
                ],
            }),
        },{
            fieldLabel: 'Area',
            name: 'area',
            xtype: 'combo',
            id: 'area',
            mode: 'local',
            valueField: 'id',
            displayField: 'name',
            triggerAction:'all',
            store: areasStore,
        },{
            fieldLabel: 'Type',
            name: 'type',
            xtype: 'textfield',
            id: 'type',
        },{
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
        }],

        buttons: [{
            text: 'Load projects',
            handler: loadProjects,
        }],

        keys: [{
            key: [Ext.EventObject.ENTER],
            handler: loadProjects,
        }],
    });

    /* Schema of the information about projects */
    var projectRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: 'description', type: 'string'},
            {name: 'activation', type: 'bool'},
            {name: 'init', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'end', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'invoice', type: 'float'},
            {name: 'totalCost', type: 'float'},
            {name: 'estHours', type: 'float'},
            {name: 'workedHours', type: 'float'},
            {name: 'absDev', type: 'float'},
            {name: 'percDev', type: 'float'},
            {name: 'estHourInvoice', type: 'float'},
            {name: 'totalProfit', type: 'float'},
            {name: 'hourProfit', type: 'float'},
            {name: 'workedHourInvoice', type: 'float'},
            {name: 'areaId', type: 'int'},
            {name: 'movedHours', type: 'float'},
            {name: 'schedType', type: 'string'},
            {name: 'type', type: 'string'},
            ]
    );



    /* Proxy to the services related with load/save Projects */
    var projectProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read: {url: 'services/getFilteredCustomProjectsService.php', method: 'GET'},
        },
    });

    /* Store to load/save Projects */
    var projectsStore = new Ext.data.Store({
        id: 'projectsStore',
        autoLoad: false,
        autoSave: false, //if set true, changes will be sent instantly
        storeId: 'projects',
        proxy: projectProxy,
        reader:new Ext.data.XmlReader({record: 'project', idProperty:'id' }, projectRecord),
        remoteSort: false,
        sortInfo: {
            field: 'init',
            direction: 'DESC',
        },
    });

    var projectColModel =  new Ext.grid.ColumnModel([
        {
            header: 'Name',
            sortable: true,
            dataIndex: 'description',
        },{
            header: 'Start Date',
            xtype: 'datecolumn',
            format: 'd/m/Y',
            sortable: true,
            dataIndex: 'init',
        },{
            header: 'End Date',
            xtype: 'datecolumn',
            format: 'd/m/Y',
            sortable: true,
            dataIndex: 'end',
        },{
            header: 'Activation',
            sortable: true,
            dataIndex: 'activation',
            xtype: 'booleancolumn',
            trueText: "<span style='color:green;'>Yes</span>",
            falseText: "<span style='color:red;'>No</span>",
        },{
            header: 'Area',
            sortable: true,
            dataIndex: 'areaId',
            renderer: areas,
        },{
            header: 'Invoice',
            sortable: true,
            dataIndex: 'invoice',
            xtype: 'numbercolumn',
        },{
            header: 'Total Cost',
            sortable: true,
            dataIndex: 'totalCost',
            xtype: 'numbercolumn',
        },{
            header: 'Total Profit',
            sortable: true,
            dataIndex: 'totalProfit',
            renderer: profit,
        },{
            header: 'Estimated Hours',
            sortable: true,
            dataIndex: 'estHours',
            xtype: 'numbercolumn',
        },{
            header: 'Worked Hours',
            sortable: true,
            dataIndex: 'workedHours',
            xtype: 'numbercolumn',
        },{
            header: 'Abs. Deviation',
            sortable: true,
            dataIndex: 'absDev',
            renderer: deviation,
        },{
            header: 'Deviation %',
            sortable: true,
            dataIndex: 'percDev',
            renderer: relativeDeviation,
        },{
            header: 'Moved Hours',
            sortable: true,
            dataIndex: 'movedHours',
            xtype: 'numbercolumn',
        },{
            header: 'Est. Hours Invoice',
            sortable: true,
            dataIndex: 'estHourInvoice',
            xtype: 'numbercolumn',
        },{
            header: 'Work Hours Invoice',
            sortable: true,
            dataIndex: 'workedHourInvoice',
            xtype: 'numbercolumn',
        },{
            header: 'Hour Profit',
            sortable: true,
            dataIndex: 'hourProfit',
            renderer: profit,
        },{
            header: 'Schedule',
            sortable: true,
            dataIndex: 'schedType',
        },{
            header: 'Type',
            sortable: true,
            dataIndex: 'type',
        }
    ]);

    // setup the panel for the grid of projects
    var projectGrid = new Ext.grid.GridPanel({
        id: 'projectGrid',
        renderTo: 'content',
        frame: true,
        height: 500,
        width: '100%',
        iconCls: 'silk-book',
        store: projectsStore,
        frame: true,
        title: 'Projects',
        style: 'margin-top: 10px',
        renderTo: 'content',
        loadMask: true,
        stripeRows: true,
        colModel: projectColModel,
        columnLines: true,
        buttons: [{
            text: 'Standard view',
            handler: showStandardView,
        },{
            text: 'Extended view',
            handler: showExtendedView,
        }],
    });

    // event handler for double-click on a project
    projectGrid.on('rowdblclick', function(grid, n, e) {
        window.location = 'viewProjectDetails.php?pid=' + grid.store.getAt(n).get('id');
    });

    //function to show only a subset of columns and hide the others
    function showStandardView() {
        projectColModel.setHidden(0, false);  //name
        projectColModel.setHidden(1, false);  //start
        projectColModel.setHidden(2, false);  //end
        projectColModel.setHidden(3, true);   //activation
        projectColModel.setHidden(4, true);   //area
        projectColModel.setHidden(5, false);  //invoice
        projectColModel.setHidden(6, true);   //total cost
        projectColModel.setHidden(7, true);   //total profit
        projectColModel.setHidden(8, false);  //estimated hours
        projectColModel.setHidden(9, false);  //worked hours
        projectColModel.setHidden(10, false); //abs. deviation
        projectColModel.setHidden(11, false); //deviation %
        projectColModel.setHidden(12, true);  //moved hours
        projectColModel.setHidden(13, true);  //est hours invoice
        projectColModel.setHidden(14, true);  //work hours invoice
        projectColModel.setHidden(15, false); //hour profit
        projectColModel.setHidden(16, true);  //schedule
        projectColModel.setHidden(17, true);  //type

        projectColModel.setColumnWidth(0, 300);
        projectColModel.setColumnWidth(1, 80);
        projectColModel.setColumnWidth(2, 80);
        projectColModel.setColumnWidth(5, 70);
        projectColModel.setColumnWidth(8, 95);
        projectColModel.setColumnWidth(9, 85);
        projectColModel.setColumnWidth(10, 85);
        projectColModel.setColumnWidth(11, 75);
        projectColModel.setColumnWidth(15, 70);
    }

    //function to show all the columns
    function showExtendedView() {
        projectColModel.setHidden(0, false);  //name
        projectColModel.setHidden(1, false);  //start
        projectColModel.setHidden(2, false);  //end
        projectColModel.setHidden(3, false);  //activation
        projectColModel.setHidden(4, false);  //area
        projectColModel.setHidden(5, false);  //invoice
        projectColModel.setHidden(6, false);  //total cost
        projectColModel.setHidden(7, false);  //total profit
        projectColModel.setHidden(8, false);  //estimated hours
        projectColModel.setHidden(9, false);  //worked hours
        projectColModel.setHidden(10, false); //abs. deviation
        projectColModel.setHidden(11, false); //deviation %
        projectColModel.setHidden(12, false); //moved hours
        projectColModel.setHidden(13, false); //est hours invoice
        projectColModel.setHidden(14, false); //work hours invoice
        projectColModel.setHidden(15, false); //hour profit
        projectColModel.setHidden(16, false); //schedule
        projectColModel.setHidden(17, false); //type

        projectColModel.setColumnWidth(0, 300);
        projectColModel.setColumnWidth(1, 80);
        projectColModel.setColumnWidth(2, 80);
        projectColModel.setColumnWidth(3, 65);
        projectColModel.setColumnWidth(4, 85);
        projectColModel.setColumnWidth(5, 70);
        projectColModel.setColumnWidth(6, 70);
        projectColModel.setColumnWidth(7, 80);
        projectColModel.setColumnWidth(8, 95);
        projectColModel.setColumnWidth(9, 85);
        projectColModel.setColumnWidth(10, 85);
        projectColModel.setColumnWidth(11, 75);
        projectColModel.setColumnWidth(12, 80);
        projectColModel.setColumnWidth(13, 105);
        projectColModel.setColumnWidth(14, 112);
        projectColModel.setColumnWidth(15, 70);
        projectColModel.setColumnWidth(16, 60);
        projectColModel.setColumnWidth(17, 65);
    }

    //hide the advanced columns
    showStandardView();

});

</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
