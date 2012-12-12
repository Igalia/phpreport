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

$storyId = $_GET["stid"];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Story Data");
include_once("include/header.php");
include_once("include/sidebar.php");
include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/StoryVO.php');
include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

// We retrieve the Custom Story
$story = CoordinationFacade::GetCustomStory($storyId);

$users = UsersFacade::GetStoryIterationProjectAreaTodayUsers($storyId);

$taskSections = CoordinationFacade::GetStoryTaskSections($storyId);

?>

<script type="text/javascript">
Ext.apply(Ext.form.VTypes, {
    <?php

    if ($uniqueError)
        echo "duplicated : function(val, field) {

        if (val != '" . $_POST['name'] . "')
            return true;

        return false;

    },

    duplicatedText:\"A Task Story for Story " . $storyId . " with name '" . $_POST['name'] . "' already exists in DB\","; ?>
});

Ext.onReady(function(){

    var storyId = <?php echo $storyId;?>;

    <?php if ($sid) {?>

    var sessionId = <?php echo $sid;?>;

    <?php } ?>

    var App = new Ext.App({});

    Ext.QuickTips.init();


    var usersStore = new Ext.data.ArrayStore({
            id: 0,
            fields: ['id', 'login'],
            data : [
    <?php

        foreach((array)$users as $auxUser)
            echo "[{$auxUser->getId()}, '{$auxUser->getLogin()}'],";

    ?>]});

    var taskSectionsStore = new Ext.data.ArrayStore({
            id: 0,
            fields: ['id', 'name'],
            data : [
    <?php

        foreach((array)$taskSections as $taskSection)
            echo "[{$taskSection->getId()}, '{$taskSection->getName()}'],";

    ?>]});


    var windowCreate;

    var riskStore = new Ext.data.ArrayStore({
            id: 0,
            fields: ['value', 'text'],
            data : [['0', 'None'],
                ['1', 'Minimum'],
                ['2', 'Low'],
                ['3', 'Medium'],
                ['4', 'High'],
                ['5', 'Critical']
    ]});


    function risks(val){

        var record =  riskStore.getById(val);

        if (record)
            return record.get('text');
        else
            return val;

    }

    // Main Panel
    var mainPanel = new Ext.FormPanel({
        width: 250,
        labelWidth: 110,
        frame:true,
        title: 'Story Data',
        bodyStyle: 'padding:5px 5px 0',
        tools: [{
            id: 'edit',
            qtip: 'Edit this Story',
            handler: function(){
            <?php
                echo "window.location = 'storyForm.php?stid={$story->getId()}';"
            ?>
            }
        }],

        iconCls: 'silk-application-view-list',
        defaults: {
             width: 300,
             labelStyle: 'text-align: right; width: 100; font-weight:bold; padding: 0 0 0 0;'
        },
        defaultType: 'displayfield',
        items:[{
            id:'name',
            name: 'name',
               fieldLabel:'Name',
            <?php
                  echo "value:'" . $story->getName() . "'";
            ?>
            },{
                id:'accepted',
                name:'accepted',
                fieldLabel: 'Accepted',
                <?php

                    $accepted = $story->getAccepted();
                    if (isset($accepted))
                    {
                        if ($accepted == False)
                            echo "value:'No'";
                        else
                            echo "value:'Yes'";
                    }

                ?>
            },{
                id: 'estHours',
                name:'estHours',
                fieldLabel: 'Estimated Hours',
                <?php

                    echo "value:'" . $story->getEstHours() . "'";

                ?>
            },{
                id: 'workHours',
                name:'workHours',
                fieldLabel: 'Worked Hours',
                <?php

                    echo "value:'" . $story->getSpent() . "'";

                ?>
            },{
                id: 'done',
                name:'done',
                fieldLabel: 'Work % Done',
                <?php

                    echo "value:'" . round($story->getDone()*100, 4) . "'";

                ?>
                },{
                  id: 'pendingHours',
                  name: 'pendingHours',
                    fieldLabel: 'Pending Hours',
          <?php

                        echo "value:'" . $story->getToDo() . "'";

          ?>
                },{
                id: 'overrun',
                name:'overrun',
                fieldLabel: 'Overrun',
                <?php

                    echo "value:'" . round($story->getOverrun()*100, 4) . "'";

                ?>
            },{
                id: 'reviewer',
                name:'reviewer',
                fieldLabel: 'Reviewer',
                <?php

                    if ($story->getReviewer())
                    {
                        $reviewer = $story->getReviewer();

                        echo "value:'" . $reviewer->getLogin() . "'";
                    }

                ?>
            },{
                id: 'nextStory',
                name: 'nextStory',
                fieldLabel: 'Next Story',
                <?php

                    if ($story->getStoryId())
                    {
                        $nextStory = CoordinationFacade::GetStory($story->getStoryId());

                        echo "value:'" . $nextStory->getName() . "'";
                    }

                ?>
            }]
    });

    mainPanel.render(Ext.get("content"));


    taskStoriesPanel = Ext.extend(Ext.grid.GridPanel, {
        renderTo: 'content',
        iconCls: 'silk-table',
        frame: true,
        title: 'Task Stories',
        height: 200,
        width: 580,
        style: 'margin-top: 10px',

        initComponent : function() {

            // typical viewConfig
            this.viewConfig = {
                forceFit: true
            };

            // relay the Store's CRUD events into this grid so these events can be conveniently listened-to in our application-code.
            this.relayEvents(this.store, ['destroy', 'save', 'update']);

            // build toolbars and buttons.
            this.tbar = this.buildTopToolbar();

            // super
            taskStoriesPanel.superclass.initComponent.call(this);
        },

        /**
         * buildTopToolbar
         */
        buildTopToolbar : function() {
            return [{
                text: 'Add',
                id: 'addBtn',
                ref: '../addBtn',
                iconCls: 'silk-table-add',
                handler: this.onAdd,
                scope: this
            }, '-', {
                text: 'Edit',
                id: 'editBtn',
                ref: '../editBtn',
                disabled: true,
                iconCls: 'silk-table-edit',
                handler: this.onEdit,
                scope: this
            }, '-', {
                text: 'Delete',
                id: 'deleteBtn',
                ref: '../deleteBtn',
                disabled: true,
                iconCls: 'silk-table-delete',
                handler: this.onDelete,
                scope: this
            }, '-'];
        },

            /**
             * onAdd
             */
        onAdd: function(btn, ev) {
            if (!windowCreate)
                windowCreate = new Ext.Window({
                     id: 'windowCreate',
                     name: 'windowCreate',
                     title: 'Create New Task Story',
                     iconCls: 'silk-application-form-add',
                                         closeAction: 'hide',
                     closable: false,
                     animateTarget: 'addBtn',
                     modal: true,
                     width:300,
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
                        defaults: {labelStyle: 'text-align: right; width: 125px;', width: 150},
                        defaultType: 'textfield',
                        items: [{
                            fieldLabel: 'Name <font color="red">*</font>',
                            name: 'name',
                            id: 'winName',
                            allowBlank:false,
                            listeners: {
                                'change': function() {
                                    this.setValue(Trim(this.getValue()));
                                }
                            },
                        },{
                            fieldLabel: 'Risk',
                            name: 'risk',
                            id: 'winRisk',
                            xtype: 'combo',
                            allowBlank: true,
                            displayField: 'text',
                            valueField: 'value',
                            hiddenName: 'hiddenRisk',
                            store: riskStore,
                            typeAhead: true,
                            mode: 'local',
                            triggerAction: 'all',
                            emptyText:'Risk',
                            selectOnFocus:true
                        },{
                            fieldLabel: 'Estimated Hours <font color="red">*</font>',
                            name: 'estHours',
                            id: 'winEstHours',
                            xtype: 'numberfield',
                            allowBlank: false,
                            listeners: {
                                'change': function() {
                                    if ((Ext.getCmp('winPendHours').getValue() == '') && (this.getValue() != ''))
                                        Ext.getCmp('winPendHours').setValue(this.getValue());
                                }
                            },
                        },{
                            fieldLabel: 'Pending Hours',
                            name: 'pendHours',
                            id: 'winPendHours',
                            xtype: 'numberfield',
                            allowBlank: true
                        },{
                            fieldLabel: 'Start Date <font color="red">*</font>',
                            name: 'startDate',
                            id: 'winStartDate',
                            xtype: 'datefield',
                            format: 'd/m/Y',
                            vtype: 'doubledaterange',
                            allowBlank: false,
                            endDateField1: 'winEndDate',
                            endDateField2: 'winEstEndDate'
                        },{
                            fieldLabel: 'Estimated End Date <font color="red">*</font>',
                            name: 'estEndDate',
                            id: 'winEstEndDate',
                            xtype: 'datefield',
                            format: 'd/m/Y',
                            vtype: 'doubledaterange',
                            allowBlank: false,
                            startDateField: 'winStartDate',
                            endDateField: 'winEndDate'
                        },{
                            fieldLabel: 'End Date',
                            name: 'endDate',
                            id: 'winEndDate',
                            xtype: 'datefield',
                            format: 'd/m/Y',
                            vtype: 'doubledaterange',
                            allowBlank: true,
                            startDateField: 'winStartDate',
                            endDateField: 'winEstEndDate'
                        },{
                            fieldLabel: 'Developer <font color="red">*</font>',
                            name: 'developer',
                            id: 'winDeveloper',
                            xtype: 'combo',
                            forceSelection: true,
                            allowBlank: false,
                            displayField: 'login',
                            valueField: 'id',
                            hiddenName: 'hiddenDeveloper',
                            store: usersStore,
                            typeAhead: true,
                            mode: 'local',
                            triggerAction: 'all',
                            emptyText:'Select a developer...',
                            selectOnFocus:true

                        },{
                            fieldLabel: 'TaskSection',
                            name: 'taskSection',
                            id: 'winTaskSection',
                            xtype: 'combo',
                            forceSelection: true,
                            allowBlank: true,
                            displayField: 'name',
                            valueField: 'id',
                            hiddenName: 'hiddenTaskSection',
                            store: taskSectionsStore,
                            typeAhead: true,
                            mode: 'local',
                            triggerAction: 'all',
                            emptyText:'Select a Task Section...',
                            selectOnFocus:true
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
                            Ext.getCmp('winPendHours').reset();
                            Ext.getCmp('winEstHours').reset();
                            Ext.getCmp('winRisk').reset();
                            Ext.getCmp('winName').reset();
                            Ext.getCmp('winEstEndDate').reset();
                            Ext.getCmp('winDeveloper').reset();
                            Ext.getCmp('winTaskSection').reset();
                        }
                     },{
                       text: 'Accept',
                       name: "btnAcceptCreate",
                       id: "btnAcceptCreate",
                       disabled: true,
                       handler: function(){
                            var newRecord = new taskStoryRecord({

                                endDate:        Ext.getCmp('winEndDate').getValue(),
                                initDate:       Ext.getCmp('winStartDate').getValue(),
                                toDo:           Ext.getCmp('winPendHours').getValue(),
                                estHours:       Ext.getCmp('winEstHours').getValue(),
                                risk:           Ext.getCmp('winRisk').getValue(),
                                name:           Ext.getCmp('winName').getValue(),
                                estEndDate:     Ext.getCmp('winEstEndDate').getValue(),
                                user:           Ext.getCmp('winDeveloper').getRawValue(),
                                userId:         Ext.getCmp('winDeveloper').getValue(),
                                taskSection:    Ext.getCmp('winTaskSection').getRawValue(),
                                taskSectionId:  Ext.getCmp('winTaskSection').getValue(),

                            });

                            store.add([newRecord]);

                            store.save();

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
                            Ext.getCmp('winName').focus('', 100);
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
            if (this.getSelectionModel().getCount() > 0)
            {
                var selected = this.getSelectionModel().getSelected();

                var windowUpdate = new Ext.Window({
                    id: 'windowUpdate',
                    name: 'windowUpdate',
                    title: 'Update Task Story',
                    iconCls: 'silk-application-form-edit',
                                        closeAction: 'hide',
                    closable: false,
                    animateTarget: 'editBtn',
                    modal: true,
                    width:300,
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
                        defaults: {labelStyle: 'text-align: right; width: 125px;', width: 150},
                        defaultType: 'textfield',

                        items: [{
                                fieldLabel: 'Name <font color="red">*</font>',
                                name: 'name',
                                id: 'win2Name',
                                value: selected.data.name,
                                allowBlank:false,
                                listeners: {
                                    'change': function() {
                                        this.setValue(Trim(this.getValue()));
                                    }
                                },
                            },{
                                fieldLabel: 'Risk',
                                name: 'risk',
                                id: 'win2Risk',
                                xtype: 'combo',
                                value: selected.data.risk,
                                allowBlank: true,
                                displayField: 'text',
                                valueField: 'value',
                                hiddenName: 'hiddenRisk',
                                store: riskStore,
                                typeAhead: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText:'Risk',
                                selectOnFocus:true
                            },{
                                fieldLabel: 'Estimated Hours <font color="red">*</font>',
                                name: 'estHours',
                                id: 'win2EstHours',
                                xtype: 'numberfield',
                                value: selected.data.estHours,
                                allowBlank: false
                            },{
                                fieldLabel: 'Pending Hours',
                                name: 'pendHours',
                                id: 'win2PendHours',
                                xtype: 'numberfield',
                                value: selected.data.toDo,
                                allowBlank: true
                            },{
                                fieldLabel: 'Start Date <font color="red">*</font>',
                                name: 'startDate',
                                id: 'win2StartDate',
                                xtype: 'datefield',
                                format: 'd/m/Y',
                                startDay: 1,
                                vtype: 'doubledaterange',
                                allowBlank: false,
                                endDateField1: 'win2EndDate',
                                endDateField2: 'win2EstEndDate'
                            },{
                                fieldLabel: 'Estimated End Date <font color="red">*</font>',
                                name: 'estEndDate',
                                id: 'win2EstEndDate',
                                xtype: 'datefield',
                                format: 'd/m/Y',
                                startDay: 1,
                                vtype: 'doubledaterange',
                                allowBlank: false,
                                startDateField: 'win2StartDate',
                                endDateField: 'win2EndDate'
                            },{
                                fieldLabel: 'End Date',
                                name: 'endDate',
                                id: 'win2EndDate',
                                xtype: 'datefield',
                                format: 'd/m/Y',
                                startDay: 1,
                                vtype: 'doubledaterange',
                                allowBlank: true,
                                startDateField: 'win2StartDate',
                                endDateField: 'win2EstEndDate'
                            },{
                                fieldLabel: 'Developer <font color="red">*</font>',
                                name: 'developer',
                                id: 'win2Developer',
                                xtype: 'combo',
                                value: selected.data.userId,
                                forceSelection: true,
                                allowBlank: false,
                                displayField: 'login',
                                valueField: 'id',
                                hiddenName: 'hiddenDeveloper',
                                store: usersStore,
                                typeAhead: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText:'Select a developer...',
                                selectOnFocus:true

                            },{
                                fieldLabel: 'TaskSection',
                                name: 'taskSection',
                                id: 'win2TaskSection',
                                xtype: 'combo',
                                value: selected.data.taskSectionId,
                                forceSelection: true,
                                allowBlank: true,
                                displayField: 'name',
                                valueField: 'id',
                                hiddenName: 'hiddenTaskSection',
                                store: taskSectionsStore,
                                typeAhead: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText:'Select a Task Section...',
                                selectOnFocus:true,
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
                            Ext.getCmp('win2PendHours').reset();
                            Ext.getCmp('win2EstHours').reset();
                            Ext.getCmp('win2Risk').reset();
                            Ext.getCmp('win2Name').reset();
                            Ext.getCmp('win2Developer').reset();
                            Ext.getCmp('win2TaskSection').reset();

                            var selected = Ext.getCmp('taskStoryGrid').getSelectionModel().getSelected();

                            Ext.getCmp('win2StartDate').setRawValue(selected.data.initDate.format('d/m/Y'));
                            Ext.getCmp('win2StartDate').validate();
                            if(selected.data.endDate)
                            {
                                Ext.getCmp('win2EndDate').setRawValue(selected.data.endDate.format('d/m/Y'));
                                Ext.getCmp('win2EndDate').validate();
                            } else Ext.getCmp('win2EndDate').reset();
                            Ext.getCmp('win2EstEndDate').setRawValue(selected.data.estEndDate.format('d/m/Y'));
                            Ext.getCmp('win2EstEndDate').validate();

                        }
                     },{
                       text: 'Accept',
                       name: "btnAcceptUpdate",
                       id: "btnAcceptUpdate",
                       handler: function(){

                        selected.set('endDate', Ext.getCmp('win2EndDate').getValue());
                        selected.set('initDate', Ext.getCmp('win2StartDate').getValue());
                        selected.set('toDo', Ext.getCmp('win2PendHours').getValue());
                        selected.set('estHours', Ext.getCmp('win2EstHours').getValue());
                        selected.set('risk', Ext.getCmp('win2Risk').getValue());
                        selected.set('name', Ext.getCmp('win2Name').getValue());
                        selected.set('estEndDate', Ext.getCmp('win2EstEndDate').getValue());
                        selected.set('user', Ext.getCmp('win2Developer').getRawValue());
                        selected.set('userId', Ext.getCmp('win2Developer').getValue());
                        selected.set('taskSectionId', Ext.getCmp('win2TaskSection').getValue());
                        selected.set('taskSection', Ext.getCmp('win2TaskSection').getRawValue());

                        store.save();

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
                            Ext.getCmp('win2Name').focus('', 100);
                        }
                    }
                });

                windowUpdate.show();

                Ext.getCmp('win2StartDate').setRawValue(selected.data.initDate.format('d/m/Y'));
                Ext.getCmp('win2StartDate').validate();
                if(selected.data.endDate)
                {
                    Ext.getCmp('win2EndDate').setRawValue(selected.data.endDate.format('d/m/Y'));
                    Ext.getCmp('win2EndDate').validate();
                }
                Ext.getCmp('win2EstEndDate').setRawValue(selected.data.estEndDate.format('d/m/Y'));
                Ext.getCmp('win2EstEndDate').validate();

             }
        },
    /**
     * onDelete
     */
        onDelete: function() {
            Ext.Msg.show({
                title: 'Confirm',
        msg: 'Are you sure you want to delete the selected Task Stories?',
        buttons: Ext.Msg.YESNO,
        iconCls: 'silk-delete',
                fn: function(btn){

                        if(btn == 'yes'){
                            var records = taskStoryGrid.getSelectionModel().getSelections();

                            for (var record=0; record < records.length; record++)
                                store.remove(records[record]);

                            store.save();
                        }

                    },
                animEl: 'deleteBtn',
                icon: Ext.Msg.QUESTION,
                closable: false,
            });
        }

    });


    /* Schema of the information about task stories */
    var taskStoryRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: "name", type: 'string'},
            {name: "risk", type: 'int', useNull: true},
            {name: "estHours", type: 'float'},
            {name: "workHours", mapping: 'spent', type: 'float'},
            {name: "toDo", mapping: 'toDo', type: 'float'},
            {name: "taskSection", mapping: 'taskSection/name', type: 'string'},
            {name: "taskSectionId", mapping: 'taskSection/id', type: 'int', useNull: true},
            {name: "endDate", type: 'date', dateFormat: 'Y-m-d'},
            {name: "estEndDate", type: 'date', dateFormat: 'Y-m-d'},
            {name: "initDate", type: 'date', dateFormat: 'Y-m-d'},
            {name: "user", mapping: 'developer/login', type: 'string'},
            {name: "userId", mapping: 'developer/id', type: 'int', useNull: true}]
    );



    /* Proxy to the services related with load/save task stories */
    var myProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getStoryCustomTaskStoriesService.php', method: 'GET'},
            create    : 'services/createTaskStoriesService.php',
            update  : 'services/updateTaskStoriesService.php',
            destroy : 'services/deleteTaskStoriesService.php'

        },
    });

    /* Store to load/save Task Stories */
    var store = new Ext.data.Store({
        id: 'taskStoriesStore',
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
            'stid': storyId, <?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
        storeId: 'id',
        proxy: myProxy,
        reader:new Ext.data.XmlReader({record: 'taskStory', idProperty:'id' }, taskStoryRecord),
        writer:new Ext.data.XmlWriter({
            xmlEncoding: 'UTF-8',
            writeAllFields: true,
            root: 'taskStories',
            tpl:'<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
                '<tpl if="records.length&gt;0">' +
                    '<tpl if="root"><{root}>' +
                        '<tpl for="records"><{parent.record}>' +
                            '<storyId>' + storyId  + '</storyId>' +
                            '<tpl for=".">' +
                                '<tpl if="name!=\'developer/login\'">' +
                                    '<tpl if="name!=\'taskSection/name\'">' +
                                        '<tpl if="name==\'developer/id\'">' +
                                            '<userId>{value}</userId>' +
                                        '</tpl>' +
                                        '<tpl if="name!=\'developer/id\'">' +
                                            '<tpl if="name==\'taskSection/id\'">' +
                                                '<taskSectionId>{value}</taskSectionId>' +
                                            '</tpl>' +
                                            '<tpl if="name!=\'taskSection/id\'">' +
                                                '<tpl if="name==\'endDate\' || name==\'initDate\' || name==\'estEndDate\'">' +
                                                    '<tpl if="value">' +
                                                        '<{name}>{[values.value.format("Y-m-d")]}</{name}>' +
                                                    '</tpl>' +
                                                '</tpl>' +
                                                '<tpl if="name!=\'endDate\' && name!=\'initDate\' && name!=\'estEndDate\'">' +
                                                    '<{name}>{value}</{name}>' +
                                                '</tpl>' +
                                            '</tpl>' +
                                        '</tpl>' +
                                    '</tpl>' +
                                '</tpl>' +
                            '</tpl>' +
                        '</{parent.record}></tpl>' +
                    '</{root}></tpl>' +
                '</tpl>'
            }, taskStoryRecord),
        remoteSort: false,
        listeners: {
            'write': function() {
                App.setAlert(true, "Task Stories Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            }
        }
    });


    var taskStoryColumns =  [
        {header: "Name", width: 40, sortable: true, dataIndex: 'name'},
        {header: "Risk", width: 18, renderer: risks, sortable: true, dataIndex: 'risk'},
        {header: "Est. Hours", width: 20, sortable: true, dataIndex: 'estHours'},
        {header: "Worked Hours", width: 27, sortable: true, dataIndex: 'workHours'},
        {header: "Pending Hours", width: 27, sortable: true, dataIndex: 'toDo'},
        {header: "Init Date", width: 25, xtype: 'datecolumn', format: 'd-m-Y', sortable: true, dataIndex: 'initDate'},
        {header: "Est. End Date", width: 25, xtype: 'datecolumn', format: 'd-m-Y', sortable: true, dataIndex: 'estEndDate'},
        {header: "End Date", width: 25, xtype: 'datecolumn', format: 'd-m-Y', sortable: true, dataIndex: 'endDate'},
        {header: "Developer", width: 25, sortable: true, dataIndex: 'user'},
        {header: "Task Section", width: 50, sortable: true, dataIndex: 'taskSection'}
    ];


    var taskStoryGrid = new taskStoriesPanel({
        id: 'taskStoryGrid',
        renderTo: 'content',
        width: 900,
        loadMask: true,
        columnLines: true,
        store: store,
        columns : taskStoryColumns
    });

    taskStoryGrid.getSelectionModel().on('selectionchange', function(sm){
        taskStoryGrid.deleteBtn.setDisabled(sm.getCount() < 1);
        taskStoryGrid.editBtn.setDisabled(sm.getCount() < 1);
    });



    <?php

    // We get the Story's Custom Task Stories
    $taskStories = CoordinationFacade::GetStoryCustomTaskStories($storyId);

    foreach((array)$taskStories as $taskStory)
    {

            // We get this Task Story's Tasks
            $tasks = CoordinationFacade::GetTaskStoryTasks($taskStory->getId());

            // This variable will contain a global description of the tasks
            $description = "";

            foreach((array)$tasks as $task)
            {

                // We retrieve the author of the Task
                $author = UsersFacade::GetUser($task->getUserId());

                // We concat information about the task on the global description
                $description = $description . "-----> Author:" . $author->getLogin() . " || Date: " . $task->getDate()->format("d-m-Y") . "\\n" . $task->getText() . "\\n";

            }

    }


     if ($description != "")
         {
    ?>

    // Main Panel
    new Ext.FormPanel({
        width: 300,
        labelWidth: 75,
        frame:true,
        title: 'Tasks Description',
        bodyStyle: 'padding:5px 5px 0',
        defaultType: 'textarea',
        items:[{
            id:'description',
            name: 'description',
            anchor: '100%',
            hideLabel: true,
            readOnly: true,
            value: <?php echo "'" . $description . "'"; ?>
        }]
    }).render(Ext.get("content"));

    <?php

        }

?>
         });
</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
