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
]);

Ext.onReady(function(){
    var projectsPanel = new Ext.FormPanel({
        labelWidth: 100,
        frame: true,
        width: 350,
        renderTo: 'content',
        defaults: {width: 230},
        items: [
            new Ext.form.ComboBox({
                fieldLabel: 'Project name',
                parent: this,
                store: new Ext.data.Store({
                    parent: this,
                    autoLoad: true,  //initial data are loaded in the application init
                    autoSave: false, //if set true, changes will be sent instantly
                    baseParams: {
                        'login': user,
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
            })
        ]
    });
});