/*
 * Copyright (C) 2011 Igalia, S.L. <info@igalia.com>
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

/***********************
 *     Data stores
 ***********************/

var citiesStore = new Ext.data.ArrayStore({
    // store configs
    autoDestroy: true,
    storeId: 'myStore',
    data: citiesArray,
    // reader configs
    idIndex: 0,
    fields: [
       {name: 'id', type: 'int'},
       'name'
    ]
});

var datesStore = new Ext.data.Store({
    parent: this,
    autoLoad: false, //data will be loaded on event
    autoSave: false, //data will be saved on event
    baseParams: {
        dateFormat: 'Y/m/d'
    },
    proxy: new Ext.data.HttpProxy({
        url: 'services/getCommonEventsByCityIdJsonService.php',
        method: 'GET'
    }),
    reader:new Ext.data.JsonReader({
        root: 'result',
        idProperty: 'id',
        fields: [
            'date'
        ]
    }),
    remoteSort: false,
});

/***********************
 *       Widgets
 ***********************/

//calendar
var calendar = new Ext.ux.DatePickerPlus({
    allowMouseWheel: false,
    showWeekNumber: true,
    multiSelection: true,
    selectedDates: [],
    noOfMonth: 12,
    noOfMonthPerRow: 4,
    startDay: 1,
    showToday: false,
    renderTodayButton: false,
    renderOkUndoButtons: false,
    value: new Date(2011, 0, 1),
    minDate: new Date(2011, 0, 1),
    maxDate: new Date(2011, 11, 31),
    listeners: {
        'select': function (item, date) {
        }
    }
});

//combo box to select a city
var citiesSelector = new Ext.form.ComboBox({
    store: citiesStore,
    valueField: 'id',
    displayField: 'name',
    mode: 'local'
});

//side bar
var sidebarPanel = new Ext.Panel({
    width: 204,
    frame: true,
    title: 'Actions',
    defaults: {
        width: '100%',
    },
    items: [
        citiesSelector,
        new Ext.menu.Separator(),
        new Ext.Button({
            text:'Save',
        })
    ],
});

/***********************
 *   Event listeners
 ***********************/

/**
 * Fired when a city is selected in the combo box
 */
citiesSelector.on('select', function () {
    datesStore.reload({
        params: {
            cityId: this.getValue()
        }
    });
});

/**
 * Fired when a new city is loaded: update the dates selected on the calendar
 */
datesStore.on('load', function () {
    var dates = [];
    for(var i=0; i<datesStore.getCount(); i++) {
        dates[i] = new Date(datesStore.getAt(i).data.date);
    }
    calendar.setValue(dates);
});

/***********************
 *        Render
 ***********************/

Ext.onReady(function (){

    Ext.QuickTips.init();

    calendar.render('content');
    sidebarPanel.render('sidebar-panel');
});
