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


$sid = $_GET["sid"];

/* We check authentication and authorization */
require_once('phpreport/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Projects Evaluation");
include_once("include/header.php");
include_once("include/sidebar.php");
include_once('phpreport/util/ConfigurationParametersManager.php');
include_once('phpreport/util/UnknownParameterException.php');
include_once('phpreport/util/LoginManager.php');
include_once('phpreport/model/vo/ProjectVO.php');
include_once('phpreport/model/facade/ProjectsFacade.php');
include_once('phpreport/model/facade/AdminFacade.php');
include_once('phpreport/web/services/WebServicesFunctions.php');

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


    editionPanel = Ext.extend(Ext.grid.GridPanel, {
        renderTo: 'content',
        frame: true,
        height: 200,
        width: 580,

        initComponent : function() {

            // typical viewConfig
            this.viewConfig = {
                forceFit: true
            };

            // build toolbars and buttons.
            this.bbar = this.buildBottomToolbar();

            // super
            editionPanel.superclass.initComponent.call(this);

            // install event handler
            this.on('rowdblclick', function(grid, n, e) {
                window.location = 'viewProjectDetails.php?pid=' + grid.store.getAt(n).get('id');
            });

        },

        /**
         * buildBottomToolbar
         */
        buildBottomToolbar : function() {
            return ['->', {
                text: 'Show Only Active Projects',
                id: this.id + 'FilterActiveBtn',
                toggleHandler: function(button, state){
                    if (state)
                        projectsStore.filter('activation', 'true');
                    else projectsStore.clearFilter();
                },
                ref: '../filterActiveBtn2',
                iconCls: 'silk-tick',
                scope: this,
                enableToggle: true,
                }]
        },


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
            read    : {url: 'services/getAllCustomProjectsService.php', method: 'GET'},
        },
    });

    /* Store to load/save Projects */
    var projectsStore = new Ext.data.Store({
        id: 'projectsStore',
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {<?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
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
            width: 300,
            sortable: true,
            dataIndex: 'description',
        },{
            header: 'Start Date',
            width: 80,
            xtype: 'datecolumn',
            format: 'd/m/Y',
            sortable: true,
            dataIndex: 'init',
        },{
            header: 'End Date',
            width: 80,
            xtype: 'datecolumn',
            format: 'd/m/Y',
            sortable: true,
            dataIndex: 'end',
        },{
            header: 'Activation',
            width: 65,
            sortable: true,
            dataIndex: 'activation',
            xtype: 'booleancolumn',
            trueText: "<span style='color:green;'>Yes</span>",
            falseText: "<span style='color:red;'>No</span>",
        },{
            header: 'Area',
            width: 85,
            sortable: true,
            dataIndex: 'areaId',
            renderer: areas,
        },{
            header: 'Invoice',
            width: 70,
            sortable: true,
            dataIndex: 'invoice',
            xtype: 'numbercolumn',
        },{
            header: 'Total Cost',
            width: 70,
            sortable: true,
            dataIndex: 'totalCost',
            xtype: 'numbercolumn',
        },{
            header: 'Total Profit',
            width: 80,
            sortable: true,
            dataIndex: 'totalProfit',
            renderer: profit,
        },{
            header: 'Estimated Hours',
            width: 95,
            sortable: true,
            dataIndex: 'estHours',
            xtype: 'numbercolumn',
        },{
            header: 'Worked Hours',
            width: 85,
            sortable: true,
            dataIndex: 'workedHours',
            xtype: 'numbercolumn',
        },{
            header: 'Abs. Deviation',
            width: 85,
            sortable: true,
            dataIndex: 'absDev',
            renderer: deviation,
        },{
            header: 'Deviation %',
            width: 75,
            sortable: true,
            dataIndex: 'percDev',
            renderer: relativeDeviation,
        },{
            header: 'Moved Hours',
            width: 80,
            sortable: true,
            dataIndex: 'movedHours',
            xtype: 'numbercolumn',
        },{
            header: 'Est. Hours Invoice',
            width: 105,
            sortable: true,
            dataIndex: 'estHourInvoice',
            xtype: 'numbercolumn',
        },{
            header: 'Work Hours Invoice',
            width: 112,
            sortable: true,
            dataIndex: 'workedHourInvoice',
            xtype: 'numbercolumn',
        },{
            header: 'Hour Profit',
            width: 70,
            sortable: true,
            dataIndex: 'hourProfit',
            renderer: profit,
        },{
            header: 'Schedule',
            width: 60,
            sortable: true,
            dataIndex: 'schedType',
        },{
            header: 'Type',
            width: 65,
            sortable: true,
            dataIndex: 'type',
        }
    ]);

    var projectGrid = new editionPanel({
        id: 'projectGrid',
        height: 500,
        iconCls: 'silk-book',
        width: projectColModel.getTotalWidth(false),
        store: projectsStore,
        frame: true,
        title: 'Projects',
        style: 'margin-top: 10px',
        renderTo: 'content',
        loadMask: true,
        stripeRows: true,
        colModel: projectColModel,
        columnLines: true,
    });

});

</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
