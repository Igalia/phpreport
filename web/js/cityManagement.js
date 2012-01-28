/*
 * Copyright (C) 2012 Igalia, S.L. <info@igalia.com>
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


/** Client-side code for city management screen.
 *
 * @filesource
 * @package PhpReport
 * @subpackage web
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 * @author Jorge López Fernández
 */

/***********************
 *   Misc variables
 ***********************/

var App = new Ext.App({});

/***********************
 *     Data stores
 ***********************/

//schema of the information about cities
var CityRecord = new Ext.data.Record.create([
    {name: 'id', type: 'int'},
    {name: "name", type: 'string'},
]);

//store to load/save cities
var citiesStore = new Ext.data.Store({
    autoLoad: true,  //initial data are loaded in the application init
    autoSave: false, //if set true, changes will be sent instantly
    baseParams: {
    },
    proxy: new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getAllCitiesService.php', method: 'GET'},
            create  : 'services/createCitiesService.php',
            update  : 'services/updateCitiesService.php',
            destroy : 'services/deleteCitiesService.php'

        },
    }),
    reader: new Ext.data.XmlReader({record: 'city', idProperty:'id' }, CityRecord),
    writer: new Ext.data.XmlWriter({
        xmlEncoding: 'UTF-8',
        writeAllFields: true,
        root: 'cities',
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
        }, CityRecord),
    remoteSort: false,
    sortInfo: {
        field: 'name',
        direction: 'ASC',
    },
    listeners: {
        'write': function() {
            App.setAlert(true, "City changes saved");
        },
        'exception': function(){
            App.setAlert(false, "Unexpected error while saving changes");
        },
        'update': function() {
            this.save();
        },
    }
});

/***********************
 *       Widgets
 ***********************/

//help tooltip configuration
var editTooltipConf = {
    title: '<b>How To Edit</b>',
    text: "<div align='justify'>This grid has inline edition, so you just double click a row and you can edit it with no problem.<br>In edition mode, you can\'t add a new row or delete it, so you must first accept or cancel the changes, and whilst the current row has uncomitted changes you can\'t select another one.</div><div align='right'><font size='1'><i>Click outside this tooltip to close it</i></font></div>",
    autoHide: false,
}

//inline editor widget definition
var editor = Ext.extend(Ext.ux.grid.RowEditor, {
    saveText: 'Accept',
    listeners: {
        'render': function () {
            //disable add and delete buttons while editing
            this.grid.getTopToolbar().get(0).disable();
            this.grid.getTopToolbar().get(2).disable();
        },
        'show': function () {
            //disable add and delete buttons while editing
            this.grid.getTopToolbar().get(0).disable();
            this.grid.getTopToolbar().get(2).disable();
        },
        'hide': function () {
            //enable again add and disable buttons when edition ends
            this.grid.getTopToolbar().get(0).enable();
            if (this.grid.getSelectionModel().hasSelection())
                this.grid.getTopToolbar().get(2).enable();
        }
    }
});

//grid with an inline editor widget definition
var inlineEditionPanel = Ext.extend(Ext.grid.GridPanel, {
    renderTo: 'content',
    frame: true,
    height: 200,
    width: 580,

    initComponent: function () {

        // typical viewConfig
        this.viewConfig = {
            forceFit: true
        };

        // relay the Store's CRUD events into this grid
        // so these events can be conveniently listened-to in our application-code.
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
    buildTopToolbar: function () {
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
    buildBottomToolbar: function () {

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
    onDelete: function () {
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

//column model for the grid
var cityColModel =  new Ext.grid.ColumnModel([
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

var citiesEditor = new editor();

//options to build the grid, it has to be instantiated later
var citiesGridOptions = {
    height: 300,
    inlineEditor: citiesEditor,
    plugins: [citiesEditor],
    iconCls: 'silk-table',
    width: cityColModel.getTotalWidth(false),
    store: citiesStore,
    frame: true,
    title: 'Cities',
    style: 'margin-top: 10px',
    loadMask: true,
    colModel: cityColModel,
    delMsg: 'Are you sure you want to delete the selected city?',

    /**
     * onAdd
     */
    onAdd: function(btn, ev) {
        var u = new CityRecord({
            name: 'New city',
        });
        this.inlineEditor.stopEditing();
        this.store.insert(0, u);
        this.getView().refresh();
        this.getSelectionModel().selectRow(0);
        this.inlineEditor.startEditing(0);
    },
};

/***********************
 *        Render
 ***********************/

Ext.onReady(function(){

    Ext.QuickTips.init();

    //instantiate and create grid
    var citiesGrid = new inlineEditionPanel(citiesGridOptions);
    citiesGrid.getSelectionModel().on('selectionchange', function(sm){
        citiesGrid.deleteBtn.setDisabled(sm.getCount() < 1);
    });
    citiesGrid.render('content');
});
