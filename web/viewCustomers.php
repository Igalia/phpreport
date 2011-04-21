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
define('PAGE_TITLE', "PhpReport - Clients Management");
include_once("include/header.php");
include_once("include/sidebar.php");
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
include_once(PHPREPORT_ROOT . '/util/UnknownParameterException.php');
include_once(PHPREPORT_ROOT . '/util/LoginManager.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');
include_once(PHPREPORT_ROOT . '/model/facade/CustomersFacade.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

$sectors = CustomersFacade::GetAllSectors();

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

    var typesStore = new Ext.data.ArrayStore({
            id: 0,
            fields: ['id', 'name'],
            data : [['small', 'Small'],['medium', 'Medium'],['large', 'Large']]});


    function types(val){

        var record =  typesStore.getById(val);

        if (record)
            return record.get('name');
        else
            return val;

    };

    /* Schema of the information about sectors */
    var sectorRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: "name", type: 'string'},
            ]
    );



    /* Proxy to the services related with load/save Sectors */
    var sectorProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getAllSectorsService.php', method: 'GET'},
            create  : 'services/createSectorsService.php',
            update  : 'services/updateSectorsService.php',
            destroy : 'services/deleteSectorsService.php'

        },
    });

    /* Store to load/save Sectors */
    var sectorsStore = new Ext.data.Store({
        id: 'sectorsStore',
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {<?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
        storeId: 'sectors',
        proxy: sectorProxy,
        reader:new Ext.data.XmlReader({record: 'sector', idProperty:'id' }, sectorRecord),
        writer:new Ext.data.XmlWriter({
            xmlEncoding: 'UTF-8',
            writeAllFields: true,
            root: 'sectors',
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
            }, sectorRecord),
        remoteSort: false,
        sortInfo: {
            field: 'name',
            direction: 'ASC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "Sectors Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
            'update': function() {
                this.save();
            },
            'load': function() {
                customersStore.load();
            }
        }
    });

    function sectors(val){

        var record =  sectorsStore.getById(val);

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

            return [{
                    iconCls: 'silk-help',
                    id: 'editHelp',
                    text: 'How Do I Edit?',
                    tooltip: editTooltipConf
                }
            ];

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


    /* Schema of the information about customers */
    var customerRecord = new Ext.data.Record.create([
            {name: 'id', type: 'int'},
            {name: "name", type: 'string'},
            {name: 'type', type: 'string'},
            {name: 'sectorId', type: 'int'},
            {name: 'url', type: 'string'}
            ]
    );



    /* Proxy to the services related with load/save Customers */
    var customerProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getUserCustomersService.php', method: 'GET'},
            create  : 'services/createCustomersService.php',
            update  : 'services/updateCustomersService.php',
            destroy : 'services/deleteCustomersService.php'

        },
    });

    /* Store to load/save Customers */
    var customersStore = new Ext.data.Store({
        id: 'customersStore',
        autoLoad: false,
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {<?php if ($sid) {?>
            'sid': sessionId <?php } ?>
        },
        storeId: 'customers',
        proxy: customerProxy,
        reader:new Ext.data.XmlReader({record: 'customer', idProperty:'id' }, customerRecord),
        writer:new Ext.data.XmlWriter({
            xmlEncoding: 'UTF-8',
            writeAllFields: true,
            root: 'customers',
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
            }, customerRecord),
        remoteSort: false,
        sortInfo: {
            field: 'name',
            direction: 'ASC',
        },
        listeners: {
            'write': function() {
                App.setAlert(true, "Clients Changes Saved");
            },
            'exception': function(){
                App.setAlert(false, "Some Error Occurred While Saving The Changes");
            },
            'update': function() {
                this.save();
            },
            'dblclick': function(n) {
                if(n.attributes.url!=NULL)
                    window.location = n.attributes.url;
            }
        }

    });


    var customerColModel =  new Ext.grid.ColumnModel([
        {
            header: "Name",
            width: 250,
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
        {
            header: "Sector",
            width: 250,
            sortable: true,
            dataIndex: 'sectorId',
            renderer: sectors,
            editor: {
                xtype: 'combo',
                displayField: 'name',
                valueField: 'id',
                lazyRender: true,
                mode: 'local',
                triggerAction: 'all',
                store: sectorsStore,
                emptyText: 'Sector',
                selectOnFocus: true,
                typeAhead: true,
                allowBlank: false
            }
        },
        {
            header: "Type",
            width: 70,
            sortable: true,
            dataIndex: 'type',
            renderer: types,
            editor: {
                xtype: 'combo',
                displayField: 'name',
                valueField: 'id',
                lazyRender: true,
                mode: 'local',
                triggerAction: 'all',
                store: typesStore,
                emptyText: 'Type',
                selectOnFocus: true,
                typeAhead: true,
                allowBlank: false
            }
        },
        {
            header: "Url",
            width: 250,
            sortable: true,
            dataIndex: 'url',
            editor: {
                xtype: 'textfield',
                allowBlank: true,
                listeners: {
                    'change': function() {
                        this.setValue(Trim(this.getValue()));
                    }
                },
            }
        },
    ]);

    var customersEditor = new editor();

    var customerGrid = new inlineEditionPanel({
        id: 'customerGrid',
        height: 300,
        inlineEditor: customersEditor,
        plugins: [customersEditor],
        iconCls: 'silk-vcard',
        width: customerColModel.getTotalWidth(false),
        store: customersStore,
        frame: true,
        columnLines: true,
        title: 'Clients',
        style: 'margin-top: 10px',
        renderTo: 'content',
        loadMask: true,
        colModel: customerColModel,
        delMsg: 'Are you sure you want to delete the selected Clients?',

        /**
         * onAdd
         */
        onAdd: function(btn, ev) {
            var u = new customerRecord({
                name: 'New Client',
            });
            this.inlineEditor.stopEditing();
            this.store.insert(0, u);
            this.getView().refresh();
            this.getSelectionModel().selectRow(0);
            this.inlineEditor.startEditing(0);
        },

        /**
         * buildBottomToolbar
         */
        buildBottomToolbar : function() {

            return [{
                    iconCls: 'silk-help',
                    id: 'editHelp',
                    text: 'How Do I Edit?',
                    tooltip: editTooltipConf
                }, '-', {
                    id: this.id + 'UrlBtn',
                    iconCls: 'silk-world',
                    text: 'Browse URL',
                    ref: '../urlBtn',
                    disabled: true,
                    handler: this.onBrowse,
                    scope: this
                }
            ];

        },

        /**
         * onBrowse
         */
        onBrowse: function() {
            if (this.getSelectionModel().getSelected().data.url != '')
                    window.open(this.getSelectionModel().getSelected().data.url);
        },

    });

    customerGrid.getSelectionModel().on('selectionchange', function(sm){
        customerGrid.deleteBtn.setDisabled(sm.getCount() < 1);
        customerGrid.urlBtn.setDisabled(sm.getCount() != 1);
        if (sm.getCount()==1)
            customerGrid.urlBtn.setDisabled(sm.getSelected().data.url=='');
    });

    var sectorColModel =  new Ext.grid.ColumnModel([
        {
            header: "Name",
            width: 250,
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

    var sectorsEditor = new editor();

    var sectorGrid = new inlineEditionPanel({
        id: 'sectorGrid',
        height: 300,
        inlineEditor: sectorsEditor,
        plugins: [sectorsEditor],
        iconCls: 'silk-table',
        width: sectorColModel.getTotalWidth(false),
        store: sectorsStore,
        frame: true,
        title: 'Sectors',
        style: 'margin-top: 10px',
        renderTo: 'content',
        loadMask: true,
        colModel: sectorColModel,
        delMsg: 'Are you sure you want to delete the selected Sectors?',

        /**
         * onAdd
         */
        onAdd: function(btn, ev) {
            var u = new sectorRecord({
                name: 'New Sector',
            });
            this.inlineEditor.stopEditing();
            this.store.insert(0, u);
            this.getView().refresh();
            this.getSelectionModel().selectRow(0);
            this.inlineEditor.startEditing(0);
        },
    });

    sectorGrid.getSelectionModel().on('selectionchange', function(sm){
        sectorGrid.deleteBtn.setDisabled(sm.getCount() < 1);
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
