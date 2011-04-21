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
define('PAGE_TITLE', "PhpReport - Areas Management");
include_once("include/header.php");
include_once("include/sidebar.php");
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
include_once(PHPREPORT_ROOT . '/util/UnknownParameterException.php');
include_once(PHPREPORT_ROOT . '/util/LoginManager.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

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

    /* Schema of the information about areas */
    var areaRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: "name", type: 'string'},
            ]
    );



    /* Proxy to the services related with load/save areas */
    var areaProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getAllAreasService.php', method: 'GET'},
            create  : 'services/createAreasService.php',
            update  : 'services/updateAreasService.php',
            destroy : 'services/deleteAreasService.php'

        },
    });

    /* Store to load/save areas */
    var areasStore = new Ext.data.Store({
        id: 'areasStore',
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {<?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
        storeId: 'areas',
        proxy: areaProxy,
        reader:new Ext.data.XmlReader({record: 'area', idProperty:'id' }, areaRecord),
        writer:new Ext.data.XmlWriter({
            xmlEncoding: 'UTF-8',
            writeAllFields: true,
            root: 'areas',
            tpl: '<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
                '<tpl if="records.length&gt;0">' +
                    '<tpl if="root"><{root}>' +
                        '<tpl for="records"><{parent.record}>' +
                            '<tpl for=".">' +
                                '<{name}>{value}</{name}>' +
                            '</tpl>' +
                        '</{parent.record}></tpl>' +
                    '</{root}></tpl>' +
                '</tpl>'
            }, areaRecord),
        remoteSort: false,
        sortInfo: {
            field: 'name',
            direction: 'ASC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "Areas Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
            'update': function() {
                this.save();
            },
        }
    });

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
            this.tbar = this.buildTopToolbar();

            this.bbar = this.buildBottomToolbar();


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
                animEl: this.id + 'DeleteBtn',
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
                icon: Ext.Msg.QUESTION,
                closable: false,
            });
                  }

    });

    var areaColModel =  new Ext.grid.ColumnModel([
        {
            header: "Name",
            width: 225,
            sortable: true,
            dataIndex: 'name',
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
    ]);

    var areasEditor = new editor();

    var areaGrid = new inlineEditionPanel({
        id: 'areaGrid',
        height: 300,
        inlineEditor: areasEditor,
        plugins: [areasEditor],
        iconCls: 'silk-table',
        width: areaColModel.getTotalWidth(false),
        store: areasStore,
        frame: true,
        title: 'Areas',
        style: 'margin-top: 10px',
        renderTo: 'content',
        loadMask: true,
        colModel: areaColModel,
        delMsg: 'Are you sure you want to delete the selected Areas?',

        /**
         * onAdd
         */
        onAdd: function(btn, ev) {
            var u = new areaRecord({
                name: 'New Area',
            });
            this.inlineEditor.stopEditing();
            this.store.insert(0, u);
            this.getView().refresh();
            this.getSelectionModel().selectRow(0);
            this.inlineEditor.startEditing(0);
        },
    });

    areaGrid.getSelectionModel().on('selectionchange', function(sm){
        areaGrid.deleteBtn.setDisabled(sm.getCount() < 1);
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
