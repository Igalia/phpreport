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

/* Schema of the information about projects */
var projectRecord = new Ext.data.Record.create([
    {name:'id'},
    {name:'description'},
    {name:'customerId'},
    {name:'customerName'}
]);

var projectComboBox = new Ext.form.ComboBox({
    fieldLabel :'Project',
    parent: this,
    store: new Ext.data.Store({
        parent: this,
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
            'order': 'description',
            'active': 'false',
        },
        filter: function(property, value, anyMatch, caseSensitive) {
            var fn;
            if (((property == 'description') || (property == 'customerName')) && !Ext.isEmpty(value, false)) {
                value = this.data.createValueMatcher(value, anyMatch, caseSensitive);
                fn = function(r){
                    return value.test(r.data['description']) || value.test(r.data['customerName']);
                };
            } else {
                fn = this.createFilterFn(property, value, anyMatch, caseSensitive);
            }
            return fn ? this.filterBy(fn) : this.clearFilter();
        },
        proxy: new Ext.data.HttpProxy({url: 'services/getProjectsService.php', method: 'GET'}),
        reader:new Ext.data.XmlReader({record: 'project', id:'id' }, projectRecord),
        remoteSort: false,
    }),
    mode: 'local',
    valueField: 'id',
    typeAhead: false,
    triggerAction: 'all',
    forceSelection: true,
    displayField: 'description',
    tpl: '<tpl for="."><div class="x-combo-list-item" > <tpl>{description} </tpl>' +
    '<tpl if="customerName">- {customerName}</tpl></div></tpl>',
    listeners: {
        'select': function (combo, record, index) {
            customerId = null;
            selectText = record.data['description'];

            // We take customer name from the select combo, and injects its id to the taskRecord
            if (record.data['customerName']) {
                customerId = record.data['customerId'];
                selectText = record.data['description'] + " - " + record.data['customerName'];
            }
            this.setValue(selectText);
            combo.value = record.id;

            window.open('viewProjectDetails.php?pid=' + this.getValue() ,"_self")
        }
    }
});


Ext.onReady(function(){
    var projectsPanel = new Ext.FormPanel({
        labelWidth: 100,
        frame: true,
        width: 700,
        renderTo: 'content',
        defaults: {width: 580},
        items : [projectComboBox]
    })
});