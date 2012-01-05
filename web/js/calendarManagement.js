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
 *   Misc variables
 ***********************/

var defaultYear = new Date().getFullYear();

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
        method: 'POST',
        api: {
            read: {url: 'services/getCommonEventsByCityIdJsonService.php', method: 'GET'},
            create: 'services/createCommonEventsService.php',
            destroy: 'services/deleteCommonEventsService.php',
        },
    }),
    reader:new Ext.data.JsonReader({
        root: 'result',
        idProperty: 'id',
        fields: [
            'date'
        ]
    }),
    writer: new Ext.data.XmlWriter({
        xmlEncoding: 'UTF-8',
        writeAllFields: true,
        root: 'commonEvents',
        record: 'commonEvent',
        tpl: '<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
                '<tpl if="records.length &gt; 0">' +
                    '<tpl if="root"><{root}>' +
                        '<tpl for="records"><commonEvent>' +
                            '<tpl for="."><{name}>' +
                                '<tpl if="name==\'date\'">' +
                                    '{[values.value.format("Y-m-d")]}' +
                                '</tpl>' +
                                '<tpl if="name!=\'date\'">' +
                                    '{value}' +
                                '</tpl>' +
                            '</{name}></tpl>' +
                        '</commonEvent></tpl>' +
                    '</{root}></tpl>' +
                '</tpl>',
    }, CustomEventRecord),
    remoteSort: false,
});

var CustomEventRecord = new Ext.data.Record.create([
    {name: 'id', type: 'int'},
    {name: 'cityId', type: 'int'},
    {name: 'date', type: 'date', dateFormat: 'Y-m-d'},
]);

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
    value: new Date(defaultYear, 0, 1),
    minDate: new Date(defaultYear, 0, 1),
    maxDate: new Date(defaultYear, 11, 31),
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

//input to select a year
var yearSelector = new Ext.form.NumberField({
    allowDecimals: false,
    minValue: 1970,
    value: defaultYear
});

//save button
var saveButton = new Ext.Button({
    text:'Save',
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
        yearSelector,
        new Ext.menu.Separator(),
        saveButton
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
 * Update the dates selected on the calendar
 */
function updateCalendarFromStore () {
    var dates = [];
    for(var i=0,j=0; i<datesStore.getCount(); i++) {
        var date = new Date(datesStore.getAt(i).data.date);
        if(date >= calendar.minDate && date <= calendar.maxDate) {
            dates[j] = date;
            j++;
        }
    }
    calendar.setValue(dates);
}

/**
 * Fired when a new city is loaded
 */
datesStore.on('load', updateCalendarFromStore);

/**
 * Fired when the selected year changes
 */
yearSelector.on('change', function () {
    calendar.setDateLimits(new Date(this.value, 0, 1),
            new Date(this.value, 11, 31));
    calendar.update(new Date(this.value, 0, 1));
    updateCalendarFromStore();
});

saveButton.on('click', function () {
    var selectedDates = calendar.selectedDates;
    for(var i=0; i<selectedDates.length; i++) {
        var record = new CustomEventRecord({
            date: selectedDates[i],
            cityId: citiesSelector.getValue()
        });
        datesStore.add(record);
    }
    datesStore.save();
});

/***********************
 *        Render
 ***********************/

Ext.onReady(function (){

    Ext.QuickTips.init();

    calendar.render('content');
    sidebarPanel.render('sidebar-panel');
});
