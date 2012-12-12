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
define('PAGE_TITLE', "PhpReport - Projects Management");
include_once("include/header.php");
include_once("include/sidebar.php");
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
include_once(PHPREPORT_ROOT . '/util/UnknownParameterException.php');
include_once(PHPREPORT_ROOT . '/util/LoginManager.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/AdminFacade.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

//$admin = LoginManager::IsAdmin($sid);

// We retrieve the Areas
$areas = AdminFacade::GetAllAreas();

?>

<script type="text/javascript">

Ext.onReady(function(){

    <?php if ($sid) {?>

    var sessionId = <?php echo $sid;?>;

    <?php } ?>

    var App = new Ext.App({});

    // Flag for stores loading coordination
    var storesLoaded = false;

    // Flag for stores loading coordination
    var storesLoaded2 = false;

    Ext.QuickTips.init();

    var windowCreate, windowAssign, windowAssign2;

    var areasStore = new Ext.data.ArrayStore({
        id: 0,
        fields: ['id', 'name'],
        data : [
        <?php

        foreach((array)$areas as $area)
            echo "[{$area->getId()}, '{$area->getName()}'],";
        ?>
    ]});

    function areas(val){

        var record =  areasStore.getById(val);

        if (record)
            return record.get('name');
        else
            return val;

    };


    // Generic fields array to use in both store defs. related to Users
    var fields = [
        {name: 'id', type: 'int'},
        {name: 'login', type: 'string'},
    ];

    // Generic fields array to use in the store defs. for Customers
    var fields2 = [
        {name: 'id', type: 'int'},
        {name: 'name', type: 'string'},
    ];

    // Generic fields array to use in both store defs. related to Users
    var userRecord = new Ext.data.Record.create(fields);

    // Generic fields array to use in the store def. related to Customers
    var customerRecord = new Ext.data.Record.create(fields2);


    /* Proxy to the services related with load/save assigned Users */
    var assignedUsersProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getProjectUsersService.php', method: 'GET'},
            create  : 'services/assignUsersToProjectService.php',
            destroy : 'services/deassignUsersFromProjectService.php'

        },
    });

    /* Store to load/save assigned Users */
    var firstGridStore = new Ext.data.Store({
        id: 'assignedUsersStore',
        autoLoad: false,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
            <?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
        storeId: 'assignedUsers',
        proxy: assignedUsersProxy,
        reader:new Ext.data.XmlReader({record: 'user', idProperty:'id' }, userRecord),
        writer:new Ext.data.XmlWriter({xmlEncoding: 'UTF-8', writeAllFields: true, root: 'users', tpl: '<tpl for="."><' + '?xml version="{version}" encoding="{encoding}"?' + '><tpl if="records.length&gt;0"><tpl if="root"><{root}><tpl for="records"><tpl if="fields.length&gt;0"><{parent.record}><tpl for="fields"><tpl if="name==\'id\'"><{name}>{value}</{name}></tpl><tpl if="name==\'login\'"><{name}>{value}</{name}></tpl></tpl><userGroups><tpl for="fields"><tpl if="name!=\'id\'"><tpl if="name!=\'login\'"><{[values.name.replace("userGroups/", "")]}>{value}</{[values.name.replace("userGroups/", "")]}></tpl></tpl></tpl></userGroups></{parent.record}></tpl></tpl></{root}></tpl></tpl></tpl>'}, userRecord),
        remoteSort: false,
        sortInfo: {
            field: 'login',
            direction: 'ASC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "Assigned Users Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
            'load': function(){
                // We only execute the following code when both stores have
                // loaded their data.
                if (storesLoaded)
                {
                    // We remove the assigned Users from the available ones.
                    firstGridStore.each( function(record){
                        secondGridStore.remove(secondGridStore.getById(record.get('id')));
                    });

                    // We mark all available Users as dirty records, because
                    // we only care about their status against the assigned
                    // ones' store, and this way it's easier.
                    secondGridStore.each( function(record){
                        record.markDirty();
                    });

                    storesLoaded = false;
                } else storesLoaded = true;
            }
        }
    });

    /* Proxy to the services related with retrieving available Users */
    var availableUsersProxy = new Ext.data.HttpProxy({
    method: 'GET',
        api: {
            read    : {url: 'services/getTodayAreaUsersService.php'},
        },
    });

    /* Store with available Users */
    var secondGridStore = new Ext.data.Store({
        id: 'availableUsersStore',
        autoLoad: false,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
            <?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
        storeId: 'availableUsers',
        proxy: availableUsersProxy,
        reader:new Ext.data.XmlReader({record: 'user', idProperty:'id' }, userRecord),
        remoteSort: false,
        sortInfo: {
            field: 'login',
            direction: 'ASC',
        },
        listeners: {
            'load': function(){
                // We only execute the following code when both stores have
                // loaded their data.
                if (storesLoaded)
                {

                    // We remove the assigned Users from the available ones.
                    firstGridStore.each( function(record){
                        secondGridStore.remove(secondGridStore.getById(record.get('id')));
                    });

                    // We mark all available Users as dirty records, because
                    // we only care about their status against the assigned
                    // ones' store, and this way it's easier.
                    secondGridStore.each( function(record){
                        record.phantom = true;
                    });

                    storesLoaded = false;
                } else storesLoaded = true;
            }
        }
    });

    // Column Model shortcut array
    var colsUsersFirst = [
        { id : 'login', header: "Login", sortable: true, dataIndex: 'login'},
    ];

    var colsUsersSecond = [
        { id : 'login', header: "Login", sortable: true, dataIndex: 'login'},
    ];

    // declare the source Grid
    var firstGrid = new Ext.grid.GridPanel({
        ddGroup          : 'secondGridDDGroup',
        store            : firstGridStore,
        columns          : colsUsersFirst,
        enableDragDrop   : true,
        stripeRows       : true,
        autoExpandColumn : 'login',
        title            : 'Assigned People',
        loadMask         : true,
        filter           : new Array(),
        filtering        : function(record){
                                if (this.filter[record.get('id')])
                                    return false;
                                else return true;
                           }
    });

    // create the destination Grid
    var secondGrid = new Ext.grid.GridPanel({
        ddGroup          : 'firstGridDDGroup',
        store            : secondGridStore,
        columns          : colsUsersSecond,
        enableDragDrop   : true,
        stripeRows       : true,
        autoExpandColumn : 'login',
        title            : 'Available People',
        loadMask         : true,
    });


    //Simple 'border layout' panel to house both grids
    var displayPanel = new Ext.Panel({
        width        : 300,
        height       : 300,
        layout       : 'hbox',
        defaults     : { flex : 1 }, //auto stretch
        layoutConfig : { align : 'stretch' },
        items        : [
            firstGrid,
            secondGrid
        ],
    });

    /* Proxy to the services related with load/save assigned Customers */
    var assignedCustomersProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getProjectCustomersService.php', method: 'GET'},
            create  : 'services/assignCustomersToProjectService.php',
            destroy : 'services/deassignCustomersFromProjectService.php'

        },
    });

    /* Store to load/save assigned Customers */
    var firstGridStore2 = new Ext.data.Store({
        id: 'assignedCustomersStore',
        autoLoad: false,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
            <?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
        storeId: 'assignedCustomers',
        proxy: assignedCustomersProxy,
        reader:new Ext.data.XmlReader({record: 'customer', idProperty:'id' }, customerRecord),
        writer:new Ext.data.XmlWriter({xmlEncoding: 'UTF-8', writeAllFields: true, root: 'customers', tpl: '<tpl for="."><' + '?xml version="{version}" encoding="{encoding}"?' + '><tpl if="records.length&gt;0"><tpl if="root"><{root}><tpl for="records"><tpl if="fields.length&gt;0"><{parent.record}><tpl for="fields"><tpl if="name==\'id\'"><{name}>{value}</{name}></tpl></tpl></{parent.record}></tpl></tpl></{root}></tpl></tpl></tpl>'}, customerRecord),
        remoteSort: false,
        sortInfo: {
            field: 'name',
            direction: 'ASC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "Assigned Clients Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
            'load': function(){
                // We only execute the following code when both stores have
                // loaded their data.
                if (storesLoaded2)
                {
                    // We remove the assigned Customers from the available ones.
                    firstGridStore2.each( function(record){
                        secondGridStore2.remove(secondGridStore2.getById(record.get('id')));
                    });

                    // We mark all available Customers as dirty records, because
                    // we only care about their status against the assigned
                    // ones' store, and this way it's easier.
                    secondGridStore2.each( function(record){
                        record.markDirty();
                    });

                    storesLoaded2 = false;
                } else storesLoaded2 = true;
            }
        }
    });

    /* Proxy to the services related with retrieving available Customers */
    var availableCustomersProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getAllCustomersService.php', method: 'GET'},
        },
    });

    /* Store with available Customers */
    var secondGridStore2 = new Ext.data.Store({
        id: 'availableCustomersStore',
        autoLoad: false,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
            <?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
        storeId: 'availableCustomers',
        proxy: availableCustomersProxy,
        reader:new Ext.data.XmlReader({record: 'customer', idProperty:'id' }, customerRecord),
        remoteSort: false,
        sortInfo: {
            field: 'name',
            direction: 'ASC',
        },
        listeners: {
            'load': function(){
                // We only execute the following code when both stores have
                // loaded their data.
                if (storesLoaded2)
                {

                    // We remove the assigned Customers from the available ones.
                    firstGridStore2.each( function(record){
                        secondGridStore2.remove(secondGridStore2.getById(record.get('id')));
                    });

                    // We mark all available Customers as dirty records, because
                    // we only care about their status against the assigned
                    // ones' store, and this way it's easier.
                    secondGridStore2.each( function(record){
                        record.phantom = true;
                    });

                    storesLoaded2 = false;
                } else storesLoaded2 = true;
            }
        }
    });

    // Column Model shortcut array
    var colsClientsFirst = [
        { id : 'name', header: "Name", sortable: true, dataIndex: 'name'},
    ];

    var colsClientsSecond = [
        { id : 'name', header: "Name", sortable: true, dataIndex: 'name'},
    ];

    // declare the source Grid
    var firstGrid2 = new Ext.grid.GridPanel({
        ddGroup          : 'secondGridDDGroup2',
        store            : firstGridStore2,
        columns          : colsClientsFirst,
        enableDragDrop   : true,
        stripeRows       : true,
        autoExpandColumn : 'name',
        title            : 'Assigned Clients',
        loadMask         : true,
        filter           : new Array(),
        filtering        : function(record){
                                if (this.filter[record.get('id')])
                                    return false;
                                else return true;
                           }
    });

    // create the destination Grid
    var secondGrid2 = new Ext.grid.GridPanel({
        ddGroup          : 'firstGridDDGroup2',
        store            : secondGridStore2,
        columns          : colsClientsSecond,
        enableDragDrop   : true,
        stripeRows       : true,
        autoExpandColumn : 'name',
        title            : 'Available Clients',
        loadMask         : true,
    });


    //Simple 'border layout' panel to house both grids
    var displayPanel2 = new Ext.Panel({
        width        : 500,
        height       : 300,
        layout       : 'hbox',
        defaults     : { flex : 1 }, //auto stretch
        layoutConfig : { align : 'stretch' },
        items        : [
            firstGrid2,
            secondGrid2
        ],
    });


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

            // relay the Store's CRUD events into this grid so these events can be conveniently listened-to in our application-code.
            this.relayEvents(this.store, ['destroy', 'save', 'update']);

            // build toolbars and buttons.
            this.tbar = this.buildTopToolbar();
            this.bbar = this.buildBottomToolbar();

            this.on('rowdblclick', function(g, n) {
                window.location = 'viewProjectDetails.php?pid=' + g.getStore().getAt(n).get('id');
            });

            // super
            editionPanel.superclass.initComponent.call(this);
        },

        /**
         * buildTopToolbar
         */
        buildTopToolbar : function() {
            return [{
                text: 'Add',
                id: this.id + 'AddBtn',
                ref: '../addBtn',
                iconCls: this.iconCls + '-add',
                handler: this.onAdd,
                scope: this
                }, '-', {
                text: 'Edit',
                id: this.id + 'EditBtn',
                ref: '../editBtn',
                disabled: true,
                iconCls: this.iconCls + '-edit',
                handler: this.onEdit,
                scope: this
                }, '-', {
                text: 'Delete',
                id: this.id + 'DeleteBtn',
                ref: '../deleteBtn',
                disabled: true,
                iconCls: this.iconCls + '-delete',
                handler: this.onDelete,
                scope: this
                }, '-']
        },

        /**
         * buildBottomToolbar
         */
        buildBottomToolbar : function() {
            return ['->', {
                text: 'Assign Clients',
                id: this.id + 'AssignBtn2',
                ref: '../assignBtn2',
                disabled: true,
                iconCls: 'silk-vcard-link',
                handler: this.onAssign2,
                scope: this
                }, '-', {
                text: 'Assign People',
                id: this.id + 'AssignBtn',
                ref: '../assignBtn',
                disabled: true,
                iconCls: 'silk-group-link',
                handler: this.onAssign,
                scope: this
                }]
        },

        onAdd: function() {

            if (!windowCreate)
                windowCreate = new Ext.Window({
                    id: 'windowCreate',
                    name: 'windowCreate',
                    title: 'Create New Project',
                    iconCls: 'silk-application-form-add',
                    closeAction: 'hide',
                    closable: false,
                    animateTarget: 'projectGridAddBtn',
                    modal: true,
                    width:350,
                    stateful: false,
                    constrainHeader: true,
                    resizable: false,
                    layout: 'form',
                    autoHeight: true,
                    plain: false,
                    items: [ new Ext.FormPanel({
                        frame:false,
                        id: 'createForm',
                        hideBorders: true,
                        monitorValid: true,
                        bodyStyle:'background-color:#D9EAF3;padding:5px 0 0 0',
                        autoWidth: true,
                        defaults: {labelStyle: 'text-align: right; width: 125px;', width: 200},
                        defaultType: 'textfield',
                        items: [{
                            fieldLabel: 'Name <font color="red">*</font>',
                            name: 'description',
                            id: 'winDescription',
                            allowBlank:false,
                            listeners: {
                                'change': function() {
                                    this.setValue(Trim(this.getValue()));
                                }
                            },
                        },{
                            fieldLabel: 'Area <font color="red">*</font>',
                            name: 'area',
                            id: 'winArea',
                            xtype: 'combo',
                            allowBlank: false,
                            displayField: 'name',
                            valueField: 'id',
                            hiddenName: 'hiddenArea',
                            store: areasStore,
                            typeAhead: true,
                            mode: 'local',
                            triggerAction: 'all',
                            emptyText:'Area',
                            selectOnFocus:true
                        },{
                            fieldLabel: 'Activation',
                            name: 'activation',
                            id: 'winActivation',
                            xtype: 'checkbox',
                        },{
                            fieldLabel: 'Invoice',
                            name: 'invoice',
                            id: 'winInvoice',
                            xtype: 'numberfield',
                        },{
                            fieldLabel: 'Estimated Hours',
                            name: 'estHours',
                            id: 'winEstHours',
                            xtype: 'numberfield',
                        },{
                            fieldLabel: 'Moved Hours',
                            name: 'movedHours',
                            id: 'winMovedHours',
                            xtype: 'numberfield',
                            allowBlank: true
                        },{
                            fieldLabel: 'Start Date',
                            name: 'startDate',
                            id: 'winStartDate',
                            xtype: 'datefield',
                            format: 'd/m/Y',
                            startDay: 1,
                            vtype: 'daterange',
                            allowBlank: true,
                            endDateField: 'winEndDate',
                        },{
                            fieldLabel: 'End Date',
                            name: 'endDate',
                            id: 'winEndDate',
                            xtype: 'datefield',
                            format: 'd/m/Y',
                            startDay: 1,
                            vtype: 'daterange',
                            allowBlank: true,
                            startDateField: 'winStartDate',
                        },{
                            fieldLabel: 'Schedule',
                            name: 'schedule',
                            id: 'winSchedule',
                            allowBlank: true,
                            listeners: {
                                'change': function() {
                                    this.setValue(Trim(this.getValue()));
                                }
                            },
                        },{
                            fieldLabel: 'Type',
                            name: 'type',
                            id: 'winType',
                            allowBlank: true,
                            listeners: {
                                'change': function() {
                                    this.setValue(Trim(this.getValue()));
                                }
                            },
                        },{
                            xtype: 'label',
                            html: '<font color="red">*</font> Required fields',
                            style: 'padding: 5px 0 5px 10px'
                        }],
                        listeners: {'clientvalidation': function(panel, valid){
                            if (valid) Ext.getCmp('btnAcceptCreate').enable();
                            else Ext.getCmp('btnAcceptCreate').disable();
                        }}
                    })],
                    buttons: [{
                        text: 'Reset',
                        name: 'btnResetCreate',
                        id: 'btnResetCreate',
                        tooltip: 'Resets all the fields to empty values.',
                        handler: function(){
                            Ext.getCmp('winEndDate').reset();
                            Ext.getCmp('winStartDate').reset();
                            Ext.getCmp('winMovedHours').reset();
                            Ext.getCmp('winEstHours').reset();
                            Ext.getCmp('winArea').reset();
                            Ext.getCmp('winDescription').reset();
                            Ext.getCmp('winSchedule').reset();
                            Ext.getCmp('winType').reset();
                            Ext.getCmp('winActivation').reset();
                            Ext.getCmp('winInvoice').reset();
                        }
                    },{
                        text: 'Accept',
                        name: "btnAcceptCreate",
                        id: "btnAcceptCreate",
                        disabled: true,
                        handler: function(){
                            var newRecord = new projectRecord({

                                end:            Ext.getCmp('winEndDate').getValue(),
                                init:           Ext.getCmp('winStartDate').getValue(),
                                movedHours:     Ext.getCmp('winMovedHours').getValue(),
                                estHours:       Ext.getCmp('winEstHours').getValue(),
                                areaId:         Ext.getCmp('winArea').getValue(),
                                description:    Ext.getCmp('winDescription').getValue(),
                                schedType:      Ext.getCmp('winSchedule').getValue(),
                                type:           Ext.getCmp('winType').getValue(),
                                activation:     Ext.getCmp('winActivation').getValue(),
                                invoice:        Ext.getCmp('winInvoice').getValue(),

                            });

                            projectsStore.add([newRecord]);

                            projectsStore.save();

                            Ext.getCmp("windowCreate").hide();

                        }
                    },{
                       text: 'Cancel',
                       name: "btnCancelCreate",
                       id: "btnCancelCreate",
                       handler: function(){
                            Ext.getCmp("windowCreate").hide();
                       }
                    }],
                    listeners: {
                        'show': function(){
                            Ext.getCmp('winDescription').focus('', 100);
                        }
                    }
                }).show();
            else {
                windowCreate.center();
                windowCreate.show();
            }

        },

        /**
         * onEdit
         */
        onEdit: function(btn, ev) {
            if (this.getSelectionModel().getCount() > 0) {

                var selected = this.getSelectionModel().getSelected();

                var windowUpdate = new Ext.Window({
                    id: 'windowUpdate',
                    name: 'windowUpdate',
                    title: 'Update Project',
                    iconCls: 'silk-application-form-edit',
                    closeAction: 'hide',
                    closable: false,
                    animateTarget: 'projectGridEditBtn',
                    modal: true,
                    width:350,
                    constrainHeader: true,
                    resizable: false,
                    layout: 'form',
                    autoHeight: true,
                    stateful: false,
                    plain: false,
                    items: [ new Ext.FormPanel({
                        frame:false,
                        hideBorders: true,
                        bodyStyle:'background-color:#D9EAF3;padding:5px 0 0 0',
                        autoWidth: true,
                        monitorValid: true,
                        defaults: {labelStyle: 'text-align: right; width: 125px;', width: 200},
                        defaultType: 'textfield',

                        items: [{
                            fieldLabel: 'Name <font color="red">*</font>',
                            name: 'description',
                            id: 'win2Description',
                            allowBlank:false,
                            value: selected.data.description,
                            listeners: {
                                'change': function() {
                                    this.setValue(Trim(this.getValue()));
                                }
                            },
                        },{
                            fieldLabel: 'Area <font color="red">*</font>',
                            name: 'area',
                            id: 'win2Area',
                            xtype: 'combo',
                            allowBlank: false,
                            displayField: 'name',
                            valueField: 'id',
                            hiddenName: 'hiddenArea',
                            store: areasStore,
                            typeAhead: true,
                            mode: 'local',
                            triggerAction: 'all',
                            emptyText:'Area',
                            selectOnFocus:true,
                            value: selected.data.areaId,
                        },{
                            fieldLabel: 'Activation',
                            name: 'activation',
                            id: 'win2Activation',
                            xtype: 'checkbox',
                            checked: selected.data.activation,
                        },{
                            fieldLabel: 'Invoice',
                            name: 'invoice',
                            id: 'win2Invoice',
                            value: selected.data.invoice,
                            xtype: 'numberfield',
                        },{
                            fieldLabel: 'Estimated Hours',
                            name: 'estHours',
                            id: 'win2EstHours',
                            xtype: 'numberfield',
                            value: selected.data.estHours,
                        },{
                            fieldLabel: 'Moved Hours',
                            name: 'movedHours',
                            id: 'win2MovedHours',
                            xtype: 'numberfield',
                            value: selected.data.movedHours,
                        },{
                            fieldLabel: 'Start Date',
                            name: 'startDate',
                            id: 'win2StartDate',
                            xtype: 'datefield',
                            format: 'd/m/Y',
                            startDay: 1,
                            vtype: 'daterange',
                            allowBlank: true,
                            endDateField: 'win2EndDate',
                        },{
                            fieldLabel: 'End Date',
                            name: 'endDate',
                            id: 'win2EndDate',
                            xtype: 'datefield',
                            format: 'd/m/Y',
                            startDay: 1,
                            vtype: 'daterange',
                            allowBlank: true,
                            startDateField: 'win2StartDate',
                        },{
                            fieldLabel: 'Schedule',
                            name: 'schedule',
                            id: 'win2Schedule',
                            allowBlank: true,
                            value: selected.data.schedule,
                            listeners: {
                                'change': function() {
                                    this.setValue(Trim(this.getValue()));
                                }
                            },
                        },{
                            fieldLabel: 'Type',
                            name: 'type',
                            id: 'win2Type',
                            allowBlank: true,
                            value: selected.data.type,
                            listeners: {
                                'change': function() {
                                    this.setValue(Trim(this.getValue()));
                                }
                            },
                        },{
                            xtype: 'label',
                            html: '<font color="red">*</font> Required fields',
                            style: 'padding: 5px 0 5px 10px'
                        }],
                        listeners: {'clientvalidation': function(panel, valid){
                            if (valid) Ext.getCmp('btnAcceptUpdate').enable();
                            else Ext.getCmp('btnAcceptUpdate').disable();
                        }}
                    })],
                    buttons: [{
                        text: 'Reset',
                        name: 'btnResetUpdate',
                        id: 'btnResetUpdate',
                        tooltip: 'Resets all the fields to their original values.',
                        handler: function(){
                            Ext.getCmp('win2Description').reset();
                            Ext.getCmp('win2Area').reset();
                            Ext.getCmp('win2Type').reset();
                            Ext.getCmp('win2MovedHours').reset();
                            Ext.getCmp('win2EstHours').reset();
                            Ext.getCmp('win2Activation').reset();
                            Ext.getCmp('win2Schedule').reset();
                            Ext.getCmp('win2Invoice').reset();

                            var selected = Ext.getCmp('projectGrid').getSelectionModel().getSelected();
                            if (selected.data.init)
                            {
                                Ext.getCmp('win2StartDate').setRawValue(selected.data.init.format('d/m/Y'));
                                Ext.getCmp('win2StartDate').validate();
                            } else Ext.getCmp('win2StartDate').reset();
                            if(selected.data.end)
                            {
                                Ext.getCmp('win2EndDate').setRawValue(selected.data.end.format('d/m/Y'));
                                Ext.getCmp('win2EndDate').validate();
                            } else Ext.getCmp('win2EndDate').reset();

                        }
                    },{
                       text: 'Accept',
                       name: "btnAcceptUpdate",
                       id: "btnAcceptUpdate",
                       handler: function(){

                           selected.set('end', Ext.getCmp('win2EndDate').getValue());
                           selected.set('init', Ext.getCmp('win2StartDate').getValue());
                           selected.set('movedHours', Ext.getCmp('win2MovedHours').getValue());
                           selected.set('estHours', Ext.getCmp('win2EstHours').getValue());
                           selected.set('areaId', Ext.getCmp('win2Area').getValue());
                           selected.set('type', Ext.getCmp('win2Type').getValue());
                           selected.set('schedType', Ext.getCmp('win2Schedule').getValue());
                           selected.set('activation', Ext.getCmp('win2Activation').getValue());
                           selected.set('invoice', Ext.getCmp('win2Invoice').getValue());
                           selected.set('description', Ext.getCmp('win2Description').getValue());

                           projectsStore.save();

                           Ext.getCmp("windowUpdate").hide();

                      }
                    },{
                       text: 'Cancel',
                       name: "btnCancelUpdate",
                       id: "btnCancelUpdate",
                       handler: function(){
                           Ext.getCmp("windowUpdate").hide();
                       }
                    }],
                    listeners: {
                        'hide': function(){
                            windowUpdate.close();
                        },
                        'show': function(){
                            Ext.getCmp('win2Description').focus('', 100);
                        }
                    }
                });

                windowUpdate.show();
                if (selected.data.init)
                {
                    Ext.getCmp('win2StartDate').setRawValue(selected.data.init.format('d/m/Y'));
                    Ext.getCmp('win2StartDate').validate();
                }
                if(selected.data.end)
                {
                    Ext.getCmp('win2EndDate').setRawValue(selected.data.end.format('d/m/Y'));
                    Ext.getCmp('win2EndDate').validate();
                }

            }
        },

        /**
         * onDelete
         */
        onDelete: function() {
            Ext.Msg.show({
                title: 'Confirm',
                msg: this.delMsg,
                buttons: Ext.Msg.YESNO,
                iconCls: 'silk-delete',
                fn: function(btn){

                    if(btn == 'yes'){
                        var records = this.getSelectionModel().getSelections();

                        for (var record=0; record < records.length; record++)
                            this.store.remove(records[record]);

                        this.store.save();
                    }

                },
                scope: this,
                animEl: 'projectGridDeleteBtn',
                icon: Ext.Msg.QUESTION,
                closable: false,
            });
        },

        /**
         * onAssign
         */
        onAssign: function(btn, ev) {
            if (this.getSelectionModel().getCount() > 0)
            {
                var selected = this.getSelectionModel().getSelected();

                var areaId = selected.get('areaId');

                var projectId = selected.get('id');

                if (!windowAssign) {
                    windowAssign = new Ext.Window({
                        id: 'windowAssign',
                        name: 'windowAssign',
                        title: 'Assign People',
                        iconCls: 'silk-table-relationship',
                        closeAction: 'hide',
                        closable: false,
                        animateTarget: 'projectGridAssignBtn',
                        modal: true,
                        width:314,
                        stateful: false,
                        constrainHeader: true,
                        resizable: false,
                        layout: 'form',
                        autoHeight: true,
                        plain: false,
                        items: [
                            displayPanel,
                            new Ext.Container({
                                layout: 'hbox',
                                layoutConfig: {pack: 'end', defaultMargins: "0 25px 0 0"},
                                items:[
                                    new Ext.form.Checkbox({
                                        boxLabel: 'Show all Users',
                                        handler: function(checkbox, value) {
                                            if (value)
                                            {
                                                availableUsersProxy.setUrl('services/getAllUsersService.php', true);
                                                firstGridStore.reload();
                                                // We are reloading, so no filtering
                                                firstGrid.filter = new Array();
                                                secondGridStore.reload();
                                            } else {
                                                availableUsersProxy.setUrl('services/getTodayAreaUsersService.php', true);
                                                firstGridStore.reload();
                                                // We are reloading, so no filtering
                                                firstGrid.filter = new Array();
                                                secondGridStore.reload();
                                            }
                                        }
                                    })
                                ]
                            })
                        ],
                        listeners: {
                            'show': function(){
                                // We create a new array for filtering when
                                // the window shows
                                firstGrid.filter = new Array();
                            }
                        },
                        buttons: [{
                            text: 'Reset',
                            name: 'btnResetAssign',
                            id: 'btnResetAssign',
                            tooltip: 'Resets the Users\' assignation to it\'s original state .',
                            tabIndex:1,
                            handler: function(){
                                firstGridStore.reload();
                                // We are resetting, so no filtering
                                firstGrid.filter = new Array();
                                secondGridStore.reload();
                            }
                        },{
                            text: 'Accept',
                            name: "btnAcceptAssign",
                            id: "btnAcceptAssign",
                            tabIndex: 2,
                            handler: function(){

                                // We nullify the filtering for having all
                                // records
                                firstGridStore.filterBy(function(){return true;});

                                // If a record is a member of the filter, then we
                                // remove it
                                firstGridStore.each(function(record){
                                    if (firstGrid.filter[record.get('id')])
                                        firstGrid.store.remove(record);
                                });

                                firstGridStore.save();

                                windowAssign.hide();

                            }
                        },{
                            text: 'Cancel',
                            name: "btnCancelAssign",
                            id: "btnCancelAssign",
                            tabIndex: 3,
                            handler: function(){
                                 windowAssign.hide();
                            }
                        }],
                    }).show();

                    /****
                    * Setup Drop Targets
                    ***/
                    // This will make sure we only drop to the  view scroller element
                    var firstGridDropTargetEl =  firstGrid.getView().scroller.dom;
                    var firstGridDropTarget = new Ext.dd.DropTarget(firstGridDropTargetEl, {
                        ddGroup    : 'firstGridDDGroup',
                        notifyDrop : function(ddSource, e, data){
                            var records =  ddSource.dragData.selections;
                            Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
                            for (var index = 0; index<records.length; index++)
                            {
                                var record = records[index];
                                // If a record is no phantom, then it
                                // was originally assigned, so it's not
                                // added. Instead, we stop using it's id for
                                // filtering in order to show it
                                if (!record.phantom)
                                    firstGrid.filter[record.get('id')] = false;
                                else // Otherwise, we add it
                                    firstGrid.store.add(record);
                            }
                            firstGrid.store.filterBy(firstGrid.filtering, firstGrid);
                            // We nullify the modified data marking
                            firstGrid.store.each(function(record){
                                if (record.modified != null)
                                    record.modified['login'] = undefined;
                            });
                            firstGrid.store.sort('login', 'ASC');
                            return true
                        }
                    });


                    // This will make sure we only drop to the view scroller element
                    var secondGridDropTargetEl = secondGrid.getView().scroller.dom;
                    var secondGridDropTarget = new Ext.dd.DropTarget(secondGridDropTargetEl, {
                        ddGroup    : 'secondGridDDGroup',
                        notifyDrop : function(ddSource, e, data){
                            var records =  ddSource.dragData.selections;
                            for (var index = 0; index<records.length; index++)
                            {
                                var record = records[index];
                                // If a record is no phantom, then it
                                // was originally assigned, so it's not
                                // removed. Instead, we use it's id for
                                // filtering in order to hide it
                                if (!record.phantom)
                                    firstGrid.filter[record.get('id')] = true;
                                else // Otherwise, we remove it
                                    firstGrid.store.remove(record);
                            }
                            firstGrid.store.filterBy(firstGrid.filtering, firstGrid);
                            secondGrid.store.add(records);
                            // We nullify the modified data marking
                            secondGrid.store.each(function(record){
                                if (record.modified != null)
                                    record.modified['login'] = undefined;
                            });
                            secondGrid.store.sort('login', 'ASC');
                            return true
                        }
                    });
                } else {
                    windowAssign.center();
                }

                tpl = '<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
                '<tpl if="records.length&gt;0">' +
                    '<tpl if="root">' +
                        '<{root} projectId="' + projectId + '">' +
                            '<tpl for="records"><{parent.record}>' +
                                '<tpl for=".">' +
                                    '<{name}>{value}</{name}>' +
                                '</tpl>' +
                            '</{parent.record}></tpl>' +
                        '</{root}>' +
                    '</tpl>' +
                '</tpl>';
                firstGridStore.writer.tpl = new Ext.XTemplate(tpl).compile();
                firstGridStore.load({params: {'pid': projectId}});
                secondGridStore.load({params: {'aid': areaId}});
                windowAssign.show();

            }
        },

        /**
         * onAssign2
         */
        onAssign2: function(btn, ev) {
            if (this.getSelectionModel().getCount() > 0)
            {
                var selected = this.getSelectionModel().getSelected();

                var projectId = selected.get('id');

                if (!windowAssign2) {
                    windowAssign2 = new Ext.Window({
                        id: 'windowAssign2',
                        name: 'windowAssign2',
                        title: 'Assign Clients',
                        iconCls: 'silk-table-relationship',
                        closeAction: 'hide',
                        closable: false,
                        animateTarget: 'projectGridAssignBtn2',
                        modal: true,
                        width:514,
                        stateful: false,
                        constrainHeader: true,
                        resizable: false,
                        layout: 'form',
                        autoHeight: true,
                        plain: false,
                        items: [
                            displayPanel2
                        ],
                        listeners: {
                            'show': function(){
                                // We create a new array for filtering when
                                // the window shows
                                firstGrid2.filter = new Array();
                            }
                        },
                        buttons: [{
                            text: 'Reset',
                            name: 'btnResetAssign',
                            id: 'btnResetAssign2',
                            tooltip: 'Resets the Clients\' assignation to it\'s original state .',
                            tabIndex:1,
                            handler: function(){
                                firstGridStore2.reload();
                                // We are resetting, so no filtering
                                firstGrid2.filter = new Array();
                                secondGridStore2.reload();
                            }
                        },{
                            text: 'Accept',
                            name: "btnAcceptAssign",
                            id: "btnAcceptAssign2",
                            tabIndex: 2,
                            handler: function(){

                                // We nullify the filtering for having all
                                // records
                                firstGridStore2.filterBy(function(){return true;});

                                // If a record is a member of the filter, then we
                                // remove it
                                firstGridStore2.each(function(record){
                                    if (firstGrid2.filter[record.get('id')])
                                        firstGrid2.store.remove(record);
                                });

                                firstGridStore2.save();

                                windowAssign2.hide();

                            }
                        },{
                            text: 'Cancel',
                            name: "btnCancelAssign",
                            id: "btnCancelAssign2",
                            tabIndex: 3,
                            handler: function(){
                                windowAssign2.hide();
                            }
                        }],
                    }).show();

                    /****
                    * Setup Drop Targets
                    ***/
                    // This will make sure we only drop to the  view scroller element
                    var firstGridDropTargetEl2 =  firstGrid2.getView().scroller.dom;
                    var firstGridDropTarget2 = new Ext.dd.DropTarget(firstGridDropTargetEl2, {
                        ddGroup    : 'firstGridDDGroup2',
                        notifyDrop : function(ddSource, e, data){
                            var records =  ddSource.dragData.selections;
                            Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
                            for (var index = 0; index<records.length; index++)
                            {
                                var record = records[index];
                                // If a record is no phantom, then it
                                // was originally assigned, so it's not
                                // added. Instead, we stop using it's id for
                                // filtering in order to show it
                                if (!record.phantom)
                                    firstGrid2.filter[record.get('id')] = false;
                                else // Otherwise, we add it
                                    firstGrid2.store.add(record);
                            }
                            firstGrid2.store.filterBy(firstGrid2.filtering, firstGrid2);
                            // We nullify the modified data marking
                            firstGrid2.store.each(function(record){
                                if (record.modified != null)
                                    record.modified['name'] = undefined;
                            });
                            firstGrid2.store.sort('name', 'ASC');
                            return true
                        }
                    });


                    // This will make sure we only drop to the view scroller element
                    var secondGridDropTargetEl2 = secondGrid2.getView().scroller.dom;
                    var secondGridDropTarget2 = new Ext.dd.DropTarget(secondGridDropTargetEl2, {
                        ddGroup    : 'secondGridDDGroup2',
                        notifyDrop : function(ddSource, e, data){
                            var records =  ddSource.dragData.selections;
                            for (var index = 0; index<records.length; index++)
                            {
                                var record = records[index];
                                // If a record is no phantom, then it
                                // was originally assigned, so it's not
                                // removed. Instead, we use it's id for
                                // filtering in order to hide it
                                if (!record.phantom)
                                    firstGrid2.filter[record.get('id')] = true;
                                else // Otherwise, we remove it
                                    firstGrid2.store.remove(record);
                            }
                            firstGrid2.store.filterBy(firstGrid2.filtering, firstGrid2);
                            secondGrid2.store.add(records);
                            // We nullify the modified data marking
                            secondGrid2.store.each(function(record){
                                if (record.modified != null)
                                    record.modified['name'] = undefined;
                            });
                            secondGrid2.store.sort('name', 'ASC');
                            return true
                        }
                    });
                } else {
                    windowAssign2.center();
                }

                tpl = '<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
                '<tpl if="records.length&gt;0">' +
                    '<tpl if="root">' +
                        '<{root} projectId="' + projectId + '">' +
                            '<tpl for="records"><{parent.record}>' +
                                '<tpl for=".">' +
                                    '<{name}>{value}</{name}>' +
                                '</tpl>' +
                            '</{parent.record}></tpl>' +
                        '</{root}>' +
                    '</tpl>' +
                '</tpl>';
                firstGridStore2.writer.tpl = new Ext.XTemplate(tpl).compile();
                firstGridStore2.load({params: {'pid': projectId}});
                secondGridStore2.load();
                windowAssign2.show();

            }
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
            {name: 'estHours', type: 'float'},
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
            read    : {url: 'services/getAllProjectsService.php', method: 'GET'},
            create  : 'services/createProjectsService.php',
            update  : 'services/updateProjectsService.php',
            destroy : 'services/deleteProjectsService.php'

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
        writer:new Ext.data.XmlWriter({
            xmlEncoding: 'UTF-8',
            writeAllFields: true,
            root: 'projects',
            tpl: '<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
                '<tpl if="records.length &gt; 0">' +
                '<tpl if="root"><{root}>' +
                    '<tpl for="records"><{parent.record}>' +
                        '<tpl for=".">' +
                            '<tpl if="name==\'init\' || name==\'end\'">' +
                                '<tpl if="value">' +
                                    '<{name}>{[values.value.format("Y-m-d")]}</{name}>' +
                                '</tpl>' +
                            '</tpl>' +
                            '<tpl if="name!=\'init\' && name!=\'end\'">' +
                                '<{name}>{value}</{name}>' +
                            '</tpl>' +
                        '</tpl>' +
                    '</{parent.record}></tpl>' +
                '</{root}></tpl>' +
                '</tpl>'
            }, projectRecord),
        remoteSort: false,
        sortInfo: {
            field: 'init',
            direction: 'DESC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "Projects Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
        }
    });


    var projectColModel =  new Ext.grid.ColumnModel([
        {
            header: 'Name',
            width: 300,
            sortable: true,
            dataIndex: 'description',
        },{
            header: 'Activation',
            width: 65,
            sortable: true,
            dataIndex: 'activation',
            xtype: 'booleancolumn',
            trueText: "<span style='color:green;'>Yes</span>",
            falseText: "<span style='color:red;'>No</span>",
        },{
            header: 'Invoice',
            width: 70,
            sortable: true,
            dataIndex: 'invoice',
            xtype: 'numbercolumn',
        },{
            header: 'Estimated Hours',
            width: 100,
            sortable: true,
            dataIndex: 'estHours',
            xtype: 'numbercolumn',
        },{
            header: 'Area',
            width: 85,
            sortable: true,
            dataIndex: 'areaId',
            renderer: areas,
        },{
            header: 'Moved Hours',
            width: 80,
            sortable: true,
            dataIndex: 'movedHours',
            xtype: 'numbercolumn',
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
        height: 300,
        iconCls: 'silk-book',
        width: projectColModel.getTotalWidth(false),
        store: projectsStore,
        frame: true,
        title: 'Projects',
        style: 'margin-top: 10px',
        renderTo: 'content',
        loadMask: true,
        colModel: projectColModel,
        columnLines: true,
        delMsg: 'Are you sure you want to delete the selected Projects?',
    });

    projectGrid.getSelectionModel().on('selectionchange', function(sm){
        projectGrid.deleteBtn.setDisabled(sm.getCount() < 1);
        projectGrid.editBtn.setDisabled(sm.getCount() < 1);
        projectGrid.assignBtn.setDisabled(sm.getCount() < 1);
        projectGrid.assignBtn2.setDisabled(sm.getCount() < 1);
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
