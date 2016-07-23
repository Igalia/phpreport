/*
 * Copyright (C) 2016 Igalia, S.L. <info@igalia.com>
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

var customerId = '';

/* Schema of the information about customers */
var customerRecord = new Ext.data.Record.create([
    {name:'id'},
    {name:'name'},
]);

/* Schema of the information about projects */
var projectRecord = new Ext.data.Record.create([
    {name:'id'},
    {name:'description'},
]);

var customerComboBox =  new Ext.form.ComboBox({
    parent: this,
    fieldLabel: 'Customer',
    store: new Ext.data.Store({
        parent: this,
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
            'active': 'false',
            'order': 'name',
        },
        proxy: new Ext.data.HttpProxy({url: 'services/getUserCustomersService.php', method: 'GET'}),
        reader: new Ext.data.XmlReader({record: 'customer', id: 'id'}, customerRecord),
        remoteSort: false,
    }),
    mode: 'local',
    typeAhead: true,
    valueField: 'id',
    displayField: 'name',
    triggerAction: 'all',
    forceSelection: true,
    listeners: {
        'select': function () {
            customerId = this.getValue();
            if(projectComboBox.store.baseParams['cid'] !== customerId) {
                projectComboBox.store.setBaseParam('cid', customerId);
                projectComboBox.store.setBaseParam('customerChanged', true);
                projectComboBox.store.load();
            }
        },
        'blur': function () {
            customerId = this.getValue();
            if(projectComboBox.store.baseParams['cid'] !== customerId) {
                projectComboBox.store.setBaseParam('cid', customerId);
                projectComboBox.store.setBaseParam('customerChanged', true);
                projectComboBox.store.load();
            }
        }
    }
});

var projectComboBox = new Ext.form.ComboBox({
    fieldLabel :'Project',
    parent: this,
    store: new Ext.data.Store({
        parent: this,
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
            'cid': customerId,
            'order': 'description',
            'active': 'false',
        },
        proxy: new Ext.data.HttpProxy({url: 'services/getCustomerProjectsService.php', method: 'GET'}),
        reader:new Ext.data.XmlReader({record: 'project', id:'id' }, projectRecord),
        remoteSort: false,
    }),
    mode: 'local',
    valueField: 'id',
    typeAhead: true,
    triggerAction: 'all',
    displayField: 'description',
    forceSelection: true,
    listeners: {
        'select': function () {
            window.open('viewProjectDetails.php?pid=' + this.getValue() ,"_self")
        }
    }
});


Ext.onReady(function(){
    var projectsPanel = new Ext.FormPanel({
        labelWidth: 100,
        frame: true,
        width: 440,
        renderTo: 'content',
        defaults: {width: 320},
        items : [customerComboBox, projectComboBox]
    })
});