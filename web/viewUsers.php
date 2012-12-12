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
define('PAGE_TITLE', "PhpReport - Users Management");
include_once("include/header.php");
include_once("include/sidebar.php");
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
include_once(PHPREPORT_ROOT . '/util/UnknownParameterException.php');
include_once(PHPREPORT_ROOT . '/util/LoginManager.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');
include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/AdminFacade.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

$admin = LoginManager::IsAdmin($sid);
$ldapEnabled = defined('USER_DAO') && (USER_DAO == 'HybridUserDAO');

// We retrieve the User Groups, Areas and Cities
$cities = AdminFacade::GetAllCities();
$areas = AdminFacade::GetAllAreas();

try{

    $groupNames = unserialize(ConfigurationParametersManager::getParameter('USER_GROUPS'));
    foreach ($groupNames as $groupName)
    {
        $group = new UserGroupVO();
        $group->setName($groupName);
        $groups[]=$group;
    }

} catch (UnknownParameterException $e) {
     $groups = UsersFacade::GetAllUserGroups();
}

?>

<script type="text/javascript">

Ext.onReady(function(){

    <?php if ($sid) {?>

    var sessionId = <?php echo $sid;?>;

    <?php } ?>

    var App = new Ext.App({});

    Ext.QuickTips.init();

    var editTooltipConf = {
        title: '<b>How To Edit</b>',
        text: "<div align='justify'>This grid has inline edition, so you just double click a row and you can edit it with no problem.<br>In edition mode, you can\'t add a new row or delete it, so you must first accept or cancel the changes, and whilst the current row has uncomitted changes you can\'t select another one.</div><div align='right'><font size='1'><i>Click outside this tooltip to close it</i></font></div>",
        autoHide: false,
    }

    var citiesStore = new Ext.data.ArrayStore({
            id: 0,
            fields: ['id', 'name'],
            data : [
    <?php

        foreach((array)$cities as $city)
            echo "[{$city->getId()}, '" . ucwords($city->getName()) . "'],";

    ?>]});


    function cities(val){

        var record =  citiesStore.getById(val);

        if (record)
            return record.get('name');
        else
            return val;

    };

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

    editor = Ext.extend(Ext.ux.grid.RowEditor, {
        saveText: 'Accept',
        listeners: {
            'render': function(){
                this.grid.getTopToolbar().get(0).disable();
                this.grid.getTopToolbar().get(2).disable();
            },
            'show': function(){
                this.grid.getTopToolbar().get(0).disable();
                this.grid.getTopToolbar().get(2).disable();
            },
            'hide': function(){
                this.grid.getTopToolbar().get(0).enable();
                if (this.grid.getSelectionModel().hasSelection())
                    this.grid.getTopToolbar().get(2).enable();
            }
        }
    });

    inlineEditionPanel = Ext.extend(Ext.grid.GridPanel, {
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
            <?php
                if ($admin)
                    echo "this.tbar = this.buildTopToolbar();

            this.bbar = this.buildBottomToolbar();";

            ?>


            // super
            inlineEditionPanel.superclass.initComponent.call(this);
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
                text: 'Delete',
                id: this.id + 'DeleteBtn',
                ref: '../deleteBtn',
                disabled: true,
                iconCls: this.iconCls + '-delete',
                handler: this.onDelete,
                scope: this
            }, '-'];
        },

        /**
         * buildBottomToolbar
         */
        buildBottomToolbar : function() {

            return new Ext.Toolbar({
                items:[{
                    iconCls: 'silk-help',
                    id: 'editHelp',
                    text: 'How Do I Edit?',
                    tooltip: editTooltipConf
                }
            ]});

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
                animEl: this.id + "DeleteBtn",
                icon: Ext.Msg.QUESTION,
                closable: false,
            });
                  }

    });


    /* Schema of the information about users */
    var userRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: "login", type: 'string'},
            {name: "password", type: 'string'}<?php

                foreach($groups as $group)
                    print ', {name: "' . $group->getName() . '", mapping: "userGroups/' . $group->getName() . '", defaultValue: false, type: "bool"}';

            ?>]
    );



    /* Proxy to the services related with load/save Users */
    var userProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getAllUsersService.php', method: 'GET'},
            create    : 'services/createUsersService.php',
            update  : 'services/updateUsersService.php',
            destroy : 'services/deleteUsersService.php'

        },
    });

    /* Store to load/save Users */
    var usersStore = new Ext.data.Store({
        id: 'usersStore',
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {<?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
        storeId: 'users',
        proxy: userProxy,
        reader:new Ext.data.XmlReader({record: 'user', idProperty:'id' }, userRecord),
        writer: new Ext.data.XmlWriter({
            xmlEncoding: 'UTF-8',
            writeAllFields: true,
            root: 'users',
            tpl: '<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
                '<tpl if="records.length &gt; 0">' +
                    '<tpl if="root"><{root}>' +
                        '<tpl for="records"><{parent.record}>' +
                            '<tpl for=".">' +
                                '<tpl if="name==\'id\' || name==\'login\' || name==\'password\'">' +
                                    '<{name}>{value}</{name}>' +
                                '</tpl>' +
                            '</tpl>' +
                            '<userGroups><tpl for=".">' +
                                '<tpl if="name!=\'id\' && name!=\'login\' && name!=\'password\'">' +
                                    '<{[values.name.replace("userGroups/", "")]}>' +
                                        '{value}' +
                                    '</{[values.name.replace("userGroups/", "")]}>' +
                                '</tpl>' +
                            '</tpl></userGroups>' +
                        '</{parent.record}></tpl>' +
                    '</{root}></tpl>' +
                '</tpl>'
                }, userRecord),
        remoteSort: false,
        sortInfo: {
            field: 'login',
            direction: 'ASC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "Users Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
            'update': function() {
                this.save();
            }
        }
    });


    var userColModel =  new Ext.grid.ColumnModel([
        {
            header: "Login",
            width: 100,
            sortable: true,
            dataIndex: 'login',
            editor: {
                xtype: 'textfield',
                allowBlank: false,
                listeners: {
                    'change': function() {
                        this.setValue(Trim(this.getValue()));
                    }
                },
            }
        },
        {
            header: "Password",
            width: 100,
            sortable: true,
            dataIndex: 'password',
            editor: {
                xtype: 'textfield',
                inputType: 'password',
                allowBlank: true,
                listeners: {
                    'change': function() {
                        this.setValue(Trim(this.getValue()));
                    }
                },
            }
        }<?php

                foreach($groups as $group)
                    print ', {xtype: "booleancolumn", header: "' . ucwords($group->getName()) . '", width: 100, sortable: true, align: "center", trueText: "' . "<span style='color:green;'>Yes</span>" . '", falseText: "' . "<span style='color:red;'>No</span>" . '", dataIndex: "' . $group->getName() . '", editor: { xtype: "checkbox" }}';

            ?>
    ]);

    var usersEditor = new editor();

    var userGrid = new inlineEditionPanel({
        id: 'userGrid',
        height: 300,
        <?php
                if ($admin && !$ldapEnabled)
                    echo "inlineEditor: usersEditor,
                    plugins: [usersEditor],";
        ?>
        iconCls: 'silk-user',
        width: userColModel.getTotalWidth(false),
        store: usersStore,
        frame: true,
        title: 'Users',
        style: 'margin-top: 10px',
        renderTo: 'content',
        loadMask: true,
        colModel: userColModel,
        delMsg: 'Are you sure you want to delete the selected Users?',

        /**
         * onAdd
         */
        onAdd: function(btn, ev) {
            var u = new userRecord({
                login: 'New User',
            });
            this.inlineEditor.stopEditing();
            this.store.insert(0, u);
            this.getView().refresh();
            this.getSelectionModel().selectRow(0);
            this.inlineEditor.startEditing(0);
        },
    });

    userGrid.getSelectionModel().on('selectionchange', function(sm){
        <?php
                if ($admin)
                    echo "userGrid.deleteBtn.setDisabled(sm.getCount() < 1);";
        ?>
        historiesPanel.setDisabled(sm.getCount() != 1);
        if ((sm.getCount() == 1) && (!historiesPanel.collapsed))
            if (!sm.getSelected().phantom)
                loadHistories(sm.getSelected().get('login'), sm.getSelected().get('id'));
    });

    function loadHistories(login, userId){
        tpl = '<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
                '<tpl if="records.length &gt; 0">' +
                    '<tpl if="root"><{root}>' +
                        '<tpl for="records"><{parent.record}>' +
                            '<userId>' + userId  + '</userId>' +
                            '<tpl for="."><{name}>' +
                                '<tpl if="name==\'init\' || name==\'end\'">' +
                                    '{[values.value.format("Y-m-d")]}' +
                                '</tpl>' +
                                '<tpl if="name!=\'init\' && name!=\'end\'">' +
                                    '{value}' +
                                '</tpl>' +
                            '</{name}></tpl>' +
                        '</{parent.record}></tpl>' +
                    '</{root}></tpl>' +
                '</tpl>';
        hourCostStore.writer.tpl = new Ext.XTemplate(tpl).compile();
        hourCostStore.load({params: {'uid': login}});
        areaStore.writer.tpl = new Ext.XTemplate(tpl).compile();
        areaStore.load({params: {'uid': login}});
        journeyStore.writer.tpl = new Ext.XTemplate(tpl).compile();
        journeyStore.load({params: {'uid': login}});
        cityStore.writer.tpl = new Ext.XTemplate(tpl).compile();
        cityStore.load({params: {'uid': login}});
    };


    /* Schema of the information about Hour Cost History */
    var hourCostRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: 'hourCost', type: 'float'},
            {name: 'init', type: 'date', dateFormat: 'Y-m-d', sortDir:'DESC'},
            {name: 'end', type: 'date', dateFormat: 'Y-m-d', sortDir:'DESC'},
    ]);

    /* Proxy to the services related with load/save Hour Cost History */
    var hourCostProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getUserHourCostHistoriesService.php', method: 'GET'},
            create    : 'services/createHourCostHistoriesService.php',
            update    : 'services/updateHourCostHistoriesService.php',
            destroy    : 'services/deleteHourCostHistoriesService.php',
        },
    });

    /* Store to load/save Hour Cost History */
    var hourCostStore = new Ext.data.Store({
        id: 'hourCostStore',
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {<?php if ($sid) {?>
            'sid': sessionId, <?php } ?>
        },
        storeId: 'hourCost',
        proxy: hourCostProxy,
        reader: new Ext.data.XmlReader({record: 'hourCostHistory', idProperty:'id' }, hourCostRecord),
        writer: new Ext.data.XmlWriter({xmlEncoding: 'UTF-8', writeAllFields: true, root: 'hourCostHistories'}, hourCostRecord),
        remoteSort: false,
        sortInfo: {
            field: 'init',
            direction: 'DESC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "Hour Cost History Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
            'update': function() {
                this.save();
            }
        }
    });


    var hourCostColModel =  new Ext.grid.ColumnModel([
        {
            header: "Init Date",
            width: 100,
            format: 'd/m/Y',
            sortable: true,
            xtype: 'datecolumn',
            dataIndex: 'init',
            editor: {
                id: 'hourCostInit',
                xtype: 'datefield',
                format: 'd/m/Y',
                startDay: 1,
                allowBlank: false,
                vtype:'daterange',
                endDateField: 'hourCostEnd'
            }
        },
        {
            header: "End Date",
            width: 100,
            format: 'd/m/Y',
            sortable: true,
            xtype: 'datecolumn',
            dataIndex: 'end',
            editor: {
                id: 'hourCostEnd',
                xtype: 'datefield',
                format: 'd/m/Y',
                startDay: 1,
                allowBlank: false,
                vtype:'daterange',
                startDateField: 'hourCostInit'
            }
        },
        {
            header: "Hour Cost",
            width: 100,
            sortable: true,
            dataIndex: 'hourCost',
            xtype: 'numbercolumn',
            format: '0.00',
            editor: {
                xtype: 'numberfield',
                decimalPrecision: 5,
                allowBlank: false
            }
        },
    ]);

    var hourCostEditor = new editor();

    var hourCostGrid = new inlineEditionPanel({
        <?php
              if ($admin)  echo "inlineEditor: hourCostEditor,
                    plugins: [hourCostEditor],";
        ?>
        id: 'hourCostGrid',
        iconCls: 'silk-money',
        width: 300,
        height: 146,
        store: hourCostStore,
        frame: false,
        header: false,
        title: 'Hour Cost',
        loadMask: true,
        colModel: hourCostColModel,
        delMsg: 'Are you sure you want to delete the selected Hour Cost History entries?',

        /**
         * onAdd
         */
        onAdd: function(btn, ev) {
            var date;
            if (this.store.getAt(0))
                date = this.store.getAt(0).get('end');
            for (i=1; i<this.store.getCount(); i++)
                if (date<this.store.getAt(i).get('end'))
                    date = this.store.getAt(i).get('end');
            if (!date)
                date = new Date();
            else date = date.add(Date.DAY, 1);
            var u = new hourCostRecord({
                init: date,
                end: date.add(Date.MONTH, 12).add(Date.DAY, -1),
            });
            this.inlineEditor.stopEditing();
            this.store.insert(0, u);
            this.getView().refresh();
            this.getSelectionModel().selectRow(0);
            this.inlineEditor.startEditing(0);
        },
    });

    <?php if ($admin) { ?>

    hourCostGrid.getSelectionModel().on('selectionchange', function(sm){
        hourCostGrid.deleteBtn.setDisabled(sm.getCount() < 1);
    });
    <?php } ?>


    /* Schema of the information about Journey History */
    var journeyRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: 'journey', type: 'float'},
            {name: 'init', type: 'date', dateFormat: 'Y-m-d', sortDir:'DESC'},
            {name: 'end', type: 'date', dateFormat: 'Y-m-d', sortDir:'DESC'},
    ]);

    /* Proxy to the services related with load/save Journey History */
    var journeyProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getUserJourneyHistoriesService.php', method: 'GET'},
            create    : 'services/createJourneyHistoriesService.php',
            update    : 'services/updateJourneyHistoriesService.php',
            destroy    : 'services/deleteJourneyHistoriesService.php',
        },
    });

    /* Store to load/save Journey History */
    var journeyStore = new Ext.data.Store({
        id: 'journeyStore',
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {<?php if ($sid) {?>
            'sid': sessionId, <?php } ?>
        },
        storeId: 'journeyCost',
        proxy: journeyProxy,
        reader: new Ext.data.XmlReader({record: 'journeyHistory', idProperty:'id' }, journeyRecord),
        writer: new Ext.data.XmlWriter({xmlEncoding: 'UTF-8', writeAllFields: true, root: 'journeyHistories'}, journeyRecord),
        remoteSort: false,
        sortInfo: {
            field: 'init',
            direction: 'DESC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "Journey History Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
            'update': function() {
                this.save();
            }
        }
    });


    var journeyColModel =  new Ext.grid.ColumnModel([
        {
            header: "Init Date",
            width: 100,
            format: 'd/m/Y',
            sortable: true,
            xtype: 'datecolumn',
            dataIndex: 'init',
            editor: {
                id: 'journeyInit',
                xtype: 'datefield',
                format: 'd/m/Y',
                startDay: 1,
                allowBlank: false,
                vtype:'daterange',
                endDateField: 'journeyEnd'
            }
        },
        {
            header: "End Date",
            width: 100,
            format: 'd/m/Y',
            sortable: true,
            xtype: 'datecolumn',
            dataIndex: 'end',
            editor: {
                id: 'journeyEnd',
                xtype: 'datefield',
                format: 'd/m/Y',
                startDay: 1,
                allowBlank: false,
                vtype:'daterange',
                startDateField: 'journeyInit'
            }
        },
        {
            header: "Journey",
            width: 100,
            sortable: true,
            dataIndex: 'journey',
            xtype: 'numbercolumn',
            format: '0.00',
            editor: {
                xtype: 'numberfield',
                decimalPrecision: 5,
                allowBlank: false
            }
        },
    ]);

    var journeyEditor = new editor();

    var journeyGrid = new inlineEditionPanel({
        <?php if ($admin) echo "inlineEditor: journeyEditor,
            plugins: [journeyEditor],";
        ?>
        id: 'journeyGrid',
        iconCls: 'silk-time',
        width: 300,
        store: journeyStore,
        frame: false,
        header: false,
        title: 'Journey',
        loadMask: true,
        colModel: journeyColModel,
        delMsg: 'Are you sure you want to delete the selected Journey History entries?',

        /**
         * onAdd
         */
        onAdd: function(btn, ev) {
            var date;
            if (this.store.getAt(0))
                date = this.store.getAt(0).get('end');
            for (i=1; i<this.store.getCount(); i++)
                if (date<this.store.getAt(i).get('end'))
                    date = this.store.getAt(i).get('end');
            if (!date)
                date = new Date();
            else date = date.add(Date.DAY, 1);
            var u = new journeyRecord({
                init: date,
                end: date.add(Date.MONTH, 12).add(Date.DAY, -1),
            });
            this.inlineEditor.stopEditing();
            this.store.insert(0, u);
            this.getView().refresh();
            this.getSelectionModel().selectRow(0);
            this.inlineEditor.startEditing(0);
        },
    });

    <?php if ($admin) { ?>
    journeyGrid.getSelectionModel().on('selectionchange', function(sm){
        journeyGrid.deleteBtn.setDisabled(sm.getCount() < 1);
    });
    <?php } ?>


    /* Schema of the information about Area History */
    var areaRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: 'areaId', type: 'int'},
            {name: 'init', type: 'date', dateFormat: 'Y-m-d', sortDir:'DESC'},
            {name: 'end', type: 'date', dateFormat: 'Y-m-d', sortDir:'DESC'},
    ]);

    /* Proxy to the services related with load/save Area History */
    var areaProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getUserAreaHistoriesService.php', method: 'GET'},
            create    : 'services/createAreaHistoriesService.php',
            update    : 'services/updateAreaHistoriesService.php',
            destroy    : 'services/deleteAreaHistoriesService.php',
        },
    });

    /* Store to load/save Area History */
    var areaStore = new Ext.data.Store({
        id: 'areaStore',
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {<?php if ($sid) {?>
            'sid': sessionId, <?php } ?>
        },
        storeId: 'areaCost',
        proxy: areaProxy,
        reader: new Ext.data.XmlReader({record: 'areaHistory', idProperty:'id' }, areaRecord),
        writer: new Ext.data.XmlWriter({xmlEncoding: 'UTF-8', writeAllFields: true, root: 'areaHistories'}, areaRecord),
        remoteSort: false,
        sortInfo: {
            field: 'init',
            direction: 'DESC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "Area History Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
            'update': function() {
                this.save();
            }
        }
    });


    var areaColModel =  new Ext.grid.ColumnModel([
        {
            header: "Init Date",
            width: 100,
            format: 'd/m/Y',
            sortable: true,
            xtype: 'datecolumn',
            dataIndex: 'init',
            editor: {
                id: 'areaInit',
                xtype: 'datefield',
                format: 'd/m/Y',
                startDay: 1,
                allowBlank: false,
                vtype:'daterange',
                endDateField: 'areaEnd'
            }
        },
        {
            header: "End Date",
            width: 100,
            format: 'd/m/Y',
            sortable: true,
            xtype: 'datecolumn',
            dataIndex: 'end',
            editor: {
                id: 'areaEnd',
                xtype: 'datefield',
                format: 'd/m/Y',
                startDay: 1,
                allowBlank: false,
                vtype:'daterange',
                startDateField: 'areaInit'
            }
        },
        {
            header: "Area",
            width: 100,
            sortable: true,
            dataIndex: 'areaId',
            renderer: areas,
            editor: {
                xtype: 'combo',
                displayField: 'name',
                valueField: 'id',
                lazyRender: true,
                mode: 'local',
                triggerAction: 'all',
                store: areasStore,
                emptyText: 'Area',
                selectOnFocus: true,
                typeAhead: true,
                allowBlank: false
            }
        },
    ]);

    var areaEditor = new editor();

    var areaGrid = new inlineEditionPanel({
        <?php if ($admin) echo "inlineEditor: areaEditor,
            plugins: [areaEditor],";
        ?>
        id: 'areaGrid',
        iconCls: 'silk-group',
        width: 300,
        store: areaStore,
        frame: false,
        header: false,
        title: 'Area',
        loadMask: true,
        colModel: areaColModel,
        delMsg: 'Are you sure you want to delete the selected Area History entries?',

        /**
         * onAdd
         */
        onAdd: function(btn, ev) {
            var date;
            if (this.store.getAt(0))
                date = this.store.getAt(0).get('end');
            for (i=1; i<this.store.getCount(); i++)
                if (date<this.store.getAt(i).get('end'))
                    date = this.store.getAt(i).get('end');
            if (!date)
                date = new Date();
            else date = date.add(Date.DAY, 1);
            var u = new areaRecord({
                init: date,
                end: date.add(Date.MONTH, 12).add(Date.DAY, -1),
            });
            this.inlineEditor.stopEditing();
            this.store.insert(0, u);
            this.getView().refresh();
            this.getSelectionModel().selectRow(0);
            this.inlineEditor.startEditing(0);
        },
    });

    <?php if ($admin) { ?>
    areaGrid.getSelectionModel().on('selectionchange', function(sm){
        areaGrid.deleteBtn.setDisabled(sm.getCount() < 1);
    });
    <?php } ?>


    /* Schema of the information about City History */
    var cityRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: 'cityId', type: 'int'},
            {name: 'init', type: 'date', dateFormat: 'Y-m-d', sortDir:'DESC'},
            {name: 'end', type: 'date', dateFormat: 'Y-m-d', sortDir:'DESC'},
    ]);

    /* Proxy to the services related with load/save City History */
    var cityProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getUserCityHistoriesService.php', method: 'GET'},
            create    : 'services/createCityHistoriesService.php',
            update    : 'services/updateCityHistoriesService.php',
            destroy    : 'services/deleteCityHistoriesService.php',
        },
    });

    /* Store to load/save City History */
    var cityStore = new Ext.data.Store({
        id: 'cityStore',
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {<?php if ($sid) {?>
            'sid': sessionId, <?php } ?>
        },
        storeId: 'cityCost',
        proxy: cityProxy,
        reader: new Ext.data.XmlReader({record: 'cityHistory', idProperty:'id' }, cityRecord),
        writer: new Ext.data.XmlWriter({xmlEncoding: 'UTF-8', writeAllFields: true, root: 'cityHistories'}, cityRecord),
        remoteSort: false,
        sortInfo: {
            field: 'init',
            direction: 'DESC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "City History Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
            'update': function() {
                this.save();
            }
        }
    });


    var cityColModel =  new Ext.grid.ColumnModel([
        {
            header: "Init Date",
            width: 100,
            format: 'd/m/Y',
            sortable: true,
            xtype: 'datecolumn',
            dataIndex: 'init',
            editor: {
                id: 'cityInit',
                xtype: 'datefield',
                format: 'd/m/Y',
                startDay: 1,
                allowBlank: false,
                vtype:'daterange',
                endDateField: 'cityEnd'
            }
        },
        {
            header: "End Date",
            width: 100,
            format: 'd/m/Y',
            sortable: true,
            xtype: 'datecolumn',
            dataIndex: 'end',
            editor: {
                id: 'cityEnd',
                xtype: 'datefield',
                format: 'd/m/Y',
                startDay: 1,
                allowBlank: false,
                vtype:'daterange',
                startDateField: 'cityInit'
            }
        },
        {
            header: "City",
            width: 100,
            sortable: true,
            dataIndex: 'cityId',
            renderer: cities,
            editor: {
                xtype: 'combo',
                displayField: 'name',
                valueField: 'id',
                lazyRender: true,
                mode: 'local',
                triggerAction: 'all',
                store: citiesStore,
                emptyText: 'City',
                selectOnFocus: true,
                typeAhead: true,
                allowBlank: false
            }
        },
    ]);

    var cityEditor = new editor();

    var cityGrid = new inlineEditionPanel({
        <?php
        if ($admin) echo "inlineEditor: cityEditor,
            plugins: [cityEditor],";
        ?>
        id: 'cityGrid',
        iconCls: 'silk-building',
        width: 300,
        store: cityStore,
        frame: false,
        header: false,
        title: 'City',
        loadMask: true,
        colModel: cityColModel,
        delMsg: 'Are you sure you want to delete the selected City History entries?',

        /**
         * onAdd
         */
        onAdd: function(btn, ev) {
            var date;
            if (this.store.getAt(0))
                date = this.store.getAt(0).get('end');
            for (i=1; i<this.store.getCount(); i++)
                if (date<this.store.getAt(i).get('end'))
                    date = this.store.getAt(i).get('end');
            if (!date)
                date = new Date();
            else date = date.add(Date.DAY, 1);
            var u = new cityRecord({
                init: date,
                end: date.add(Date.MONTH, 12).add(Date.DAY, -1),
            });
            this.inlineEditor.stopEditing();
            this.store.insert(0, u);
            this.getView().refresh();
            this.getSelectionModel().selectRow(0);
            this.inlineEditor.startEditing(0);
        },
    });

    <?php if ($admin) { ?>
    cityGrid.getSelectionModel().on('selectionchange', function(sm){
        cityGrid.deleteBtn.setDisabled(sm.getCount() < 1);
    });
    <?php } ?>

    var historyTabs = new Ext.TabPanel({
        autoWidth: true,
        activeTab: 0,
        //height: 200,
        frame: false,
        plain: true,
        items:[
            hourCostGrid,
            journeyGrid,
            areaGrid,
            cityGrid,
            ]
    });

    var historiesPanel = new Ext.Panel({
        title: 'User Histories',
        collapsible:true,
        iconCls: 'silk-hourglass',
        collapsed: true,
        frame: true,
        //height: 200,
        disabled: true,
        stateful: false,
        renderTo: 'content',
        width:400,
        items: [
            historyTabs
        ],
        listeners:{
            'beforeexpand': function(){
                loadHistories(userGrid.getSelectionModel().getSelected().get('login'), userGrid.getSelectionModel().getSelected().get('id'));
            }
        }
    });

    historyTabs.setActiveTab(3);
    historyTabs.setActiveTab(1);
    historyTabs.setActiveTab(2);
    historyTabs.setActiveTab(0);

});

</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
