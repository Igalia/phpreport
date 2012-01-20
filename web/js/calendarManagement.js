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
var App = new Ext.App({});
var unsavedChanges = false;

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


var CustomEventRecord = new Ext.data.Record.create([
    {name: 'id', type: 'int'},
    {name: 'cityId', type: 'int'},
    {name: 'date', type: 'date', dateFormat: 'Y-m-d'},
]);

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
            read: {url: 'services/getCommonEventsByCityIdService.php', method: 'GET'},
            create: 'services/createCommonEventsService.php',
            destroy: 'services/deleteCommonEventsService.php',
        },
    }),
    reader: new Ext.data.XmlReader({
        record: 'commonEvent',
        idProperty: 'id'
    }, CustomEventRecord),
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

/***********************
 *       Widgets
 ***********************/

//calendar
var calendar = new Ext.ux.DatePickerPlus({
    allowMouseWheel: false,
    showWeekNumber: true,
    multiSelection: true,
    multiSelectByCTRL : false,
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
    triggerAction:'all',
    mode: 'local'
});

//input to select a year
var yearSelector = new Ext.form.NumberField({
    allowDecimals: false,
    minValue: 1970,
    value: defaultYear,
    listeners: {
        specialkey: function (f,e){
            //fire change of the selector when pressing enter
            if (e.getKey() == e.ENTER) {
                yearSelector.blur();
            }
        }
    }
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
        width: 192,
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
var oldValue;
citiesSelector.on('beforeselect', function () {
    //before the calendar runs the selection, we store the old value
    oldValue = this.getValue();
});
citiesSelector.on('select', function () {
    //helper function to reload the store
    function reloadStore() {
        datesStore.reload({
            params: {
                cityId: citiesSelector.getValue()
            }
        });
    }

    if(unsavedChanges) {
        //if there were unsaved changes, ask for confirmation
        Ext.MessageBox.confirm('Confirm',
                'You will lose unsaved changes in current city. Are you sure?',
                function (btn) {
            if(btn == 'yes') {
                //confirmed
                reloadStore();
            }
            else {
                //operation cancelled, get old value back
                citiesSelector.setValue(oldValue);
            }
        });
    }
    else {
        //no need to ask for confirmation
        reloadStore();
    }
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
datesStore.on('load', function () {
    updateCalendarFromStore();
    unsavedChanges = false;
});

/**
 * Fired when the selected year changes
 */
yearSelector.on('change', function () {
    calendar.setDateLimits(new Date(this.value, 0, 1),
            new Date(this.value, 11, 31));
    calendar.update(new Date(this.value, 0, 1));
    updateCalendarFromStore();
});

/*
 * Fired when a date is selected or unselected.
 * Synchronizes the store with the calendar.
 */
calendar.on('afterdateclick', function (datePicker, date) {
    //helper function to add a date to the store
    function addDateToStore(date) {
        var record = new CustomEventRecord({
            date: date,
            cityId: citiesSelector.getValue()
        });
        datesStore.add(record);
    }

    //helper function to remove a date from the store
    function removeDateFromStore(date) {
        for(var i=0; i<datesStore.getCount(); i++) {
            var dateStore = new Date(datesStore.getAt(i).data.date);
            if(date.getTime() == dateStore.getTime() &&
                    datesStore.getAt(i).data.cityId == citiesSelector.getValue()) {
                datesStore.removeAt(i);
                return;
            }
        }
    }

    unsavedChanges = true;

    var selectedDates = datePicker.selectedDates.sortDates();
    if(selectedDates.length == 0) {
        removeDateFromStore(date);
        return;
    }

    var i = 0;
    while(i<selectedDates.length && selectedDates[i].getTime() < date.getTime()) {
        i++;
    }
    if(i == selectedDates.length) {
        removeDateFromStore(date);
    }
    else if(selectedDates[i].getTime() == date.getTime()) {
        addDateToStore(date);
    }
    else {
        removeDateFromStore(date);
    }
});

/**
 * Fired when save button is pressed.
 * Triggers store save.
 */
saveButton.on('click', function () {
    datesStore.save();
});

/**
 * Fired when the AJAX response is received and it was a success.
 */
datesStore.on('write', function() {
    App.setAlert(true, "Changes saved");
    unsavedChanges = false;
});

/**
 * Fired when the AJAX response is received and it contains an error.
 */
datesStore.on('exception', function(){
    App.setAlert(false, "Unexpected error while saving changes");
});

/***********************
 *        Render
 ***********************/

Ext.onReady(function (){

    Ext.QuickTips.init();

    calendar.render('content');
    sidebarPanel.render('sidebar-panel');
});
