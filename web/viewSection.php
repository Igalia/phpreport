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

$sectionId = $_GET["scid"];

$login = $_GET['login'];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Section Data");
include_once("include/header.php");
include_once("include/sidebar.php");
include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/SectionVO.php');
include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

// We retrieve the Custom Section
$section = CoordinationFacade::GetCustomSection($sectionId);

// We get the Section's Custom Task Sections
$taskSections = CoordinationFacade::GetSectionCustomTaskSections($sectionId);

$users = UsersFacade::GetSectionModuleProjectAreaTodayUsers($sectionId);

?>

<script type="text/javascript">

Ext.onReady(function(){

    var sectionId = <?php echo $sectionId;?>;

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

    var windowCreate;

    // Main Panel
    var mainPanel = new Ext.FormPanel({
        width: 275,
        labelWidth: 100,
        frame:true,
        title: 'Section Data',
        bodyStyle: 'padding:5px 5px 0',
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
                  echo "value:'" . $section->getName() . "'";
            ?>
            },{
                id:'accepted',
                name:'accepted',
                fieldLabel: 'Accepted',
                <?php

                    $accepted = $section->getAccepted();
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

                    echo "value:'" . $section->getEstHours() . "'";

                ?>
            },{
                id: 'workHours',
                name:'workHours',
                fieldLabel: 'Worked Hours',
                <?php

                    echo "value:'" . $section->getSpent() . "'";

                ?>
            },{
                id: 'done',
                name:'done',
                fieldLabel: 'Work % Done',
                <?php

                    echo "value:'" . round($section->getDone()*100, 4) . "'";

                ?>
                },{
                  id: 'pendingHours',
                  name: 'pendingHours',
                    fieldLabel: 'Pending Hours',
          <?php

                        echo "value:'" . $section->getToDo() . "'";

          ?>
                },{
                id: 'overrun',
                name:'overrun',
                fieldLabel: 'Overrun',
                <?php

                    echo "value:'" . round($section->getOverrun()*100, 4) . "'";

                ?>
            },{
                id: 'reviewer',
                name:'reviewer',
                fieldLabel: 'Reviewer',
                <?php

                    if ($section->getReviewer())
                    {
                        $reviewer = $section->getReviewer();

                        echo "value:'" . $reviewer->getLogin() . "'";
                    }

                ?>
            },{
                id: 'text',
                name:'text',
                fieldLabel: 'Text',
                xtype:'textarea',
                width: 150,
                readOnly: true,
                grow: true,
                maxHeight: 200,
                style: 'background-color:#DFF3FE; background-image: none;',
                <?php

                    if ($section->getText())
                    {
                        echo "value:'" . $section->getText() . "'";
                    }

                ?>
            }]
    });

    mainPanel.render(Ext.get("content"));

    taskSectionsPanel = Ext.extend(Ext.grid.GridPanel, {
        renderTo: 'content',
        iconCls: 'silk-table',
        frame: true,
        title: 'Task Sections',
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
            taskSectionsPanel.superclass.initComponent.call(this);
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
                     title: 'Create New Task Section',
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
                            Ext.getCmp('winEstHours').reset();
                            Ext.getCmp('winRisk').reset();
                            Ext.getCmp('winName').reset();
                            Ext.getCmp('winDeveloper').reset();
                        }
                     },{
                       text: 'Accept',
                       name: "btnAcceptCreate",
                       id: "btnAcceptCreate",
                       disabled: true,
                       handler: function(){
                            var newRecord = new taskSectionRecord({

                                estHours:       Ext.getCmp('winEstHours').getValue(),
                                risk:           Ext.getCmp('winRisk').getValue(),
                                name:           Ext.getCmp('winName').getValue(),
                                user:           Ext.getCmp('winDeveloper').getRawValue(),
                                userId:         Ext.getCmp('winDeveloper').getValue(),

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
                    title: 'Update Task Section',
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

                            Ext.getCmp('win2EstHours').reset();
                            Ext.getCmp('win2Risk').reset();
                            Ext.getCmp('win2Name').reset();
                            Ext.getCmp('win2Developer').reset();

                        }
                     },{
                       text: 'Accept',
                       name: "btnAcceptUpdate",
                       id: "btnAcceptUpdate",
                       handler: function(){

                        selected.set('estHours', Ext.getCmp('win2EstHours').getValue());
                        selected.set('risk', Ext.getCmp('win2Risk').getValue());
                        selected.set('name', Ext.getCmp('win2Name').getValue());
                        selected.set('user', Ext.getCmp('win2Developer').getRawValue());
                        selected.set('userId', Ext.getCmp('win2Developer').getValue());

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

             }
        },
    /**
     * onDelete
     */
        onDelete: function() {
            Ext.Msg.show({
                title: 'Confirm',
        msg: 'Are you sure you want to delete the selected Task Sections?',
                buttons: Ext.Msg.YESNO,
                iconCls: 'silk-delete',
                fn: function(btn){

                        if(btn == 'yes'){
                            var records = taskSectionGrid.getSelectionModel().getSelections();

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


    /* Schema of the information about task sections */
    var taskSectionRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: "name", type: 'string'},
            {name: "risk", type: 'int', useNull: true},
            {name: "estHours", type: 'float'},
            {name: "workHours", mapping: 'spent', type: 'float'},
            {name: "toDo", mapping: 'toDo', type: 'float'},
            {name: "user", mapping: 'developer/login', type: 'string'},
            {name: "userId", mapping: 'developer/id', type: 'int', useNull: true}]
    );



    /* Proxy to the services related with load/save task sections */
    var myProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getSectionCustomTaskSectionsService.php', method: 'GET'},
            create    : 'services/createTaskSectionsService.php',
            update  : 'services/updateTaskSectionsService.php',
            destroy : 'services/deleteTaskSectionsService.php'

        },
    });

    /* Store to load/save Task Sections */
    var store = new Ext.data.Store({
        id: 'taskSectionsStore',
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
            'scid': sectionId, <?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
        storeId: 'id',
        proxy: myProxy,
        reader:new Ext.data.XmlReader({record: 'taskSection', idProperty:'id' }, taskSectionRecord),
        writer:new Ext.data.XmlWriter({
            xmlEncoding: 'UTF-8',
            writeAllFields: true,
            root: 'taskSections',
            tpl: '<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
                '<tpl if="records.length &gt; 0">' +
                    '<tpl if="root"><{root}>' +
                        '<tpl for="records"><{parent.record}>' +
                            '<sectionId>' + sectionId  + '</sectionId>' +
                            '<tpl for=".">' +
                                '<tpl if="name!=\'developer/login\'">' +
                                    '<tpl if="name==\'developer/id\'">' +
                                        '<userId>{value}</userId>' +
                                    '</tpl>' +
                                    '<tpl if="name!=\'developer/id\'">' +
                                        '<{name}>{value}</{name}>' +
                                    '</tpl>' +
                                '</tpl>' +
                            '</tpl>' +
                        '</{parent.record}></tpl>' +
                    '</{root}></tpl>' +
                '</tpl>'
            }, taskSectionRecord),
        remoteSort: false,
        listeners: {
            'write': function() {
                App.setAlert(true, "Task Sections Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            }
        }
    });


    var taskSectionColumns =  [
        {header: "Name", width: 40, sortable: true, dataIndex: 'name'},
        {header: "Risk", width: 18, renderer: risks, sortable: true, dataIndex: 'risk'},
        {header: "Est. Hours", width: 20, sortable: true, dataIndex: 'estHours'},
        {header: "Worked Hours", width: 27, sortable: true, dataIndex: 'workHours'},
        {header: "Pending Hours", width: 27, sortable: true, dataIndex: 'toDo'},
        {header: "Developer", width: 25, sortable: true, dataIndex: 'user'}
    ];


    var taskSectionGrid = new taskSectionsPanel({
        id: 'taskSectionGrid',
        renderTo: 'content',
        width: 550,
        loadMask: true,
        columnLines: true,
        store: store,
        columns : taskSectionColumns
    });

    taskSectionGrid.getSelectionModel().on('selectionchange', function(sm){
        taskSectionGrid.deleteBtn.setDisabled(sm.getCount() < 1);
        taskSectionGrid.editBtn.setDisabled(sm.getCount() < 1);
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
