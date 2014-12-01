<?php
/*
 * Copyright (C) 2009-2014 Igalia, S.L. <info@igalia.com>
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

<script src="include/ext.ux.form.superboxselect/SuperBoxSelect.js"></script>
<link rel="stylesheet" type="text/css" href="include/ext.ux.form.superboxselect/superboxselect.css"/>
<script type="text/javascript">

Ext.onReady(function(){

    <?php if ($sid) {?>

    var sessionId = <?php echo $sid;?>;

    <?php } ?>

    var App = new Ext.App({});

    // Flags to coordinate the multi-combo widget setup with the stores
    var storesLoaded = false;
    var clientStoresLoaded = false;

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
    var assignedUsersStore = new Ext.data.Store({
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
                var userSelector = Ext.getCmp('userSelector');
                userSelector.clearValue(true); //clear, don't trigger remove event
                assignedUsersStore.each(function (record) {
                    userSelector.addNewItem(record.data);
                });
                storesLoaded = true;
            }
        }
    });

    /* Store with available Users */
    var availableUsersStore = new Ext.data.Store({
        id: 'availableUsersStore',
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        storeId: 'availableUsers',
        proxy: new Ext.data.HttpProxy({
            method: 'GET',
            api: {
                read: {url: 'services/getAllUsersService.php'}
            },
        }),
        reader: new Ext.data.XmlReader({
                record: 'user',
                idProperty:'id'
            }, userRecord),
        remoteSort: false,
        sortInfo: {
            field: 'login',
            direction: 'ASC',
        }
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
    var assignedClientsStore = new Ext.data.Store({
        id: 'assignedClientsStore',
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
                var selector = Ext.getCmp('clientSelector');
                selector.clearValue(true); //clear, don't trigger remove event
                assignedClientsStore.each(function (record) {
                    selector.addNewItem(record.data);
                });
                clientStoresLoaded = true;
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
    var availableClientsStore = new Ext.data.Store({
        id: 'availableClientsStore',
        autoLoad: true,  //initial data are loaded in the application init
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
                this.onEdit();
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
                }, '-', {
                text: 'Details',
                id: this.id + 'DetailsBtn',
                ref: '../detailsBtn',
                disabled: true,
                iconCls: this.iconCls + '-go',
                handler: this.onDetails,
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
                    closable: true,
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
                    closable: true,
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
         * onDetails
         */
        onDetails: function() {

            if (this.getSelectionModel().getCount() > 0) {
                var selected = this.getSelectionModel().getSelected();
                window.location = 'viewProjectDetails.php?pid=' + selected.id;
            }
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
                        closable: true,
                        animateTarget: 'projectGridAssignBtn',
                        modal: true,
                        width: 320,
                        height: 240,
                        stateful: false,
                        constrainHeader: true,
                        resizable: true,
                        layout: 'fit',
                        autoScroll: true,
                        plain: false,
                        items: [{
                            id: 'userSelector',
                            xtype: 'superboxselect',
                            fieldLabel: 'Project members',
                            emptyText: 'Select users...',
                            name: 'users',
                            store: availableUsersStore,
                            mode: 'local',
                            valueField: 'id',
                            displayField: 'login',
                            forceSelection : true,
                            allowBlank: true,
                            listeners: {
                                'addItem': function (selector, value, record) {
                                    //wait until the assigned users store is ready
                                    if(!storesLoaded) return;

                                    record.phantom = true;
                                    assignedUsersStore.add(record);
                                },
                                'removeItem': function (selector, value, record) {
                                    //wait until the assigned users store is ready
                                    if(!storesLoaded) return;

                                    //assignedUsersStore.remove(record) will not work,
                                    //because the record is not a member of assignedUsersStore
                                    //workaround: get the record with the same id
                                    //contained in assignedUsersStore and remove it
                                    assignedUsersStore.remove(
                                        assignedUsersStore.getById(record.data['id']));
                                },
                            },
                        }],
                        listeners: {
                            'show': function () {
                                Ext.getCmp('userSelector').focus('', 100);
                            }
                        },
                        buttons: [{
                            text: 'Reset',
                            name: 'btnResetAssign',
                            id: 'btnResetAssign',
                            tooltip: 'Resets the Users\' assignation to it\'s original state .',
                            handler: function(){
                                var userSelector = Ext.getCmp('userSelector');
                                storesLoaded = false;
                                userSelector.clearValue(true); //clear, don't trigger remove event
                                assignedUsersStore.rejectChanges();
                                assignedUsersStore.reload();
                            }
                        },{
                            text: 'Accept',
                            name: "btnAcceptAssign",
                            id: "btnAcceptAssign",
                            handler: function(){
                                assignedUsersStore.save();
                                windowAssign.hide();

                            }
                        },{
                            text: 'Cancel',
                            name: "btnCancelAssign",
                            id: "btnCancelAssign",
                            handler: function(){
                                assignedUsersStore.rejectChanges();
                                windowAssign.hide();
                            }
                        }],
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
                assignedUsersStore.writer.tpl = new Ext.XTemplate(tpl).compile();
                storesLoaded = false;
                assignedUsersStore.load({params: {'pid': projectId}});
                availableUsersStore.load();
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
                        closable: true,
                        animateTarget: 'projectGridAssignBtn2',
                        modal: true,
                        width: 320,
                        height: 240,
                        stateful: false,
                        constrainHeader: true,
                        resizable: true,
                        layout: 'fit',
                        autoScroll: true,
                        plain: false,
                        items: [{
                            id: 'clientSelector',
                            xtype: 'superboxselect',
                            fieldLabel: 'Assigned clients',
                            emptyText: 'Select clients...',
                            name: 'clients',
                            store: availableClientsStore,
                            mode: 'local',
                            valueField: 'id',
                            displayField: 'name',
                            forceSelection : true,
                            allowBlank: true,
                            listeners: {
                                'addItem': function (selector, value, record) {
                                    //wait until the assigned users store is ready
                                    if(!clientStoresLoaded) return;

                                    record.phantom = true;
                                    assignedClientsStore.add(record);
                                },
                                'removeItem': function (selector, value, record) {
                                    //wait until the assigned users store is ready
                                    if(!clientStoresLoaded) return;

                                    //assignedClientsStore.remove(record) will not work,
                                    //because the record is not a member of assignedClientsStore
                                    //workaround: get the record with the same id
                                    //contained in assignedClientsStore and remove it
                                    assignedClientsStore.remove(
                                        assignedClientsStore.getById(record.data['id']));
                                },
                            },
                        }],
                        listeners: {
                            'show': function () {
                                Ext.getCmp('clientSelector').focus('', 100);
                            }
                        },
                        buttons: [{
                            text: 'Reset',
                            name: 'btnResetAssign',
                            id: 'btnResetAssign2',
                            tooltip: 'Resets the Clients\' assignation to it\'s original state .',
                            handler: function(){
                                var selector = Ext.getCmp('clientSelector');
                                clientStoresLoaded = false;
                                selector.clearValue(true); //clear, don't trigger remove event
                                assignedClientsStore.rejectChanges();
                                assignedClientsStore.reload();
                            }
                        },{
                            text: 'Accept',
                            name: "btnAcceptAssign",
                            id: "btnAcceptAssign2",
                            handler: function(){
                                assignedClientsStore.save();
                                windowAssign2.hide();
                            }
                        },{
                            text: 'Cancel',
                            name: "btnCancelAssign",
                            id: "btnCancelAssign2",
                            handler: function(){
                                assignedClientsStore.rejectChanges();
                                windowAssign2.hide();
                            }
                        }],
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
                assignedClientsStore.writer.tpl = new Ext.XTemplate(tpl).compile();
                clientStoresLoaded = false;
                assignedClientsStore.load({params: {'pid': projectId}});
                availableClientsStore.load();
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
        projectGrid.detailsBtn.setDisabled(sm.getCount() < 1);
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
