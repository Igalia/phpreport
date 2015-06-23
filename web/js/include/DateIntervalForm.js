/*
 * Copyright (C) 2012-2015 Igalia, S.L. <info@igalia.com>
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


/**
 * Returns the date of the Monday previous to the received date.
 * @param date {Date}
 * @return {Date} date of the previous Monday.
 */
function getPreviousMonday(date) {
    var date = new Date(date); //clone date to avoid changes in the parameter
    var dayOfWeek = date.getDay();
    var monday = date.getDate() - dayOfWeek;
    monday += (dayOfWeek == 0 ? -6:1); // adjust when day is Sunday

    return new Date(date.setDate(monday));
}

/**
 * Returns the date of the first Sunday after the received date.
 * @param date {Date}
 * @return {Date} date of the next Sunday.
 */
function getNextSunday(date) {
    var date = new Date(date); //clone date to avoid changes in the parameter
    var dayOfWeek = date.getDay();
    var diff = (dayOfWeek == 0 ? 0 : 7 - dayOfWeek);

    return new Date(date.setDate(date.getDate() + diff));
}

/**
 * @class Ext.ux.DateIntervalForm
 * @extends Ext.Panel
 */
Ext.ux.DateIntervalForm = Ext.extend(Ext.Panel, {

    /**
     * @cfg {Date} defaultStartDate
     * Specifies the default value of the start date field.
     * It will be 1/1/1900 if not set explicitly.
     */
    defaultStartDate: new Date(1900,00,01),

    /**
     * @cfg {Date} defaultEndDate
     * Specifies the default value of the end date field.
     * It will be current date if not set explicitly.
     */
    defaultEndDate: new Date(),

    /**
     * Get the selected start date, or the default one if empty.
     * @return {Date} start date selected in the form or the default if empty.
     */
    getStartDate: function () {
        var dateField = this._getStartDateField();
        // check if the field has values, and if it doesn't, create default ones
        if (dateField.getRawValue() == "")
            return this.defaultStartDate;
        else
            return dateField.getValue();
    },

    /**
     * Get the selected end date, or the default one if empty.
     * @return {Date} end date selected in the form or the default if empty.
     */
    getEndDate: function () {
        var dateField = this._getEndDateField();
        // check if the field has values, and if it doesn't, create default ones
        if (dateField.getRawValue() == "")
            return this.defaultEndDate;
        else
            return dateField.getValue();
    },

    /**
     * Set a date for the start date field.
     * @param date {Date} start date.
     */
    setStartDate: function (date) {
        this._getStartDateField().setValue(date);
    },

    /**
     * Set a date for the end date field.
     * @param date {Date} end date.
     */
    setEndDate: function (date) {
        this._getEndDateField().setValue(date);
    },

    /**
     * Gets the field that contains the start date in this form.
     * This method is intended to be private, only used inside DateIntervalForm.
     * @return {DateField} start date field.
     */
    _getStartDateField: function () {
        return this.get('form').get('startDate');
    },

    /**
     * Gets the field that contains the end date in this form.
     * This method is intended to be private, only used inside DateIntervalForm.
     * @return {DateField} end date field.
     */
    _getEndDateField: function () {
        return this.get('form').get('endDate');
    },

    initComponent: function () {

        Ext.apply(this, {
            //default visual configuration
            frame: true,
            header: false,
            width: 350,
            items:[{
                id: 'form',
                layout: 'form',
                bodyStyle: 'padding:5px 5px 0',
                labelWidth: 75,
                defaults: {
                    width: 230
                },

                //items: start and end date fields
                items: [{
                    fieldLabel: 'Start Date',
                    name: 'start',
                    xtype: 'datefield',
                    format: 'd/m/Y',
                    startDay: 1,
                    id: 'startDate',
                    listeners: {
                        'change': function (field, newValue, oldValue) {
                            if(!field.isValid()) return;
                            var date = field.parseDate(newValue);
                            Ext.getCmp('endDate').setMinValue(date);
                        }
                    }
                },{
                    fieldLabel: 'End Date',
                    name: 'end',
                    xtype: 'datefield',
                    format: 'd/m/Y',
                    startDay: 1,
                    id: 'endDate',
                    listeners: {
                        'change': function (field, newValue, oldValue) {
                            if(!field.isValid()) return;
                            var date = field.parseDate(newValue);
                            Ext.getCmp('startDate').setMaxValue(date);
                        }
                    }
                }],

                //button: send form
                buttons: [{
                    text: 'View',
                    scope: this, //scope inside the handler will be the Form object
                    handler: function () {
                        this.fireEvent("view", this,
                            this.getStartDate(), this.getEndDate());
                    }
                }],
            }],
            bbar: {
                xtype: 'toolbar',
                items:[{
                    text: 'Last week',
                    xtype: 'button',
                    scope: this, //scope inside the handler will be the Form object
                    handler: function () {
                        var lastWeek = new Date();
                        lastWeek.setDate(lastWeek.getDate() - 7);
                        this._getStartDateField().setValue(getPreviousMonday(lastWeek));
                        this._getEndDateField().setValue(getNextSunday(lastWeek));

                        this.fireEvent("view", this,
                            this.getStartDate(), this.getEndDate());
                    }
                }, {
                    text: 'This week',
                    xtype: 'button',
                    scope: this, //scope inside the handler will be the Form object
                    handler: function () {
                        var now = new Date();
                        this._getStartDateField().setValue(getPreviousMonday(now));
                        this._getEndDateField().setValue(now);

                        this.fireEvent("view", this,
                            this.getStartDate(), this.getEndDate());
                    }
                }, '-', {
                    text: 'This month',
                    xtype: 'button',
                    scope: this, //scope inside the handler will be the Form object
                    handler: function () {
                        var date = new Date();
                        this._getEndDateField().setValue(date);
                        date.setDate(1);
                        this._getStartDateField().setValue(date);

                        this.fireEvent("view", this,
                            this.getStartDate(), this.getEndDate());
                    }
                }],
            }
        });

        this.addEvents(
            /**
             * @event view
             * Fires when the 'View' button is pressed.
             * @param {DateIntervalForm} this
             * @param {Date} start date selected in the form
             * @param {Date} end date selected in the form
             */
            'view'
        );

        /* call the superclass to preserve base class functionality */
        Ext.ux.DateIntervalForm.superclass.initComponent.apply(this, arguments);

        /* set informative tooltips for date fields */
        Ext.QuickTips.init();
        Ext.QuickTips.register({
           text: "<div align='justify'>" +
                    "<b>Format:</b> \'dd/mm/yyyy\'<br>" +
                    "<b>Inclusion:</b> included in the interval<br>" +
                    "<b>Default value if empty:</b> " +
                    this.defaultStartDate.toLocaleDateString() +"</div>",
           target: this._getStartDateField()
        });
        Ext.QuickTips.register({
           text: "<div align='justify'>" +
                    "<b>Format:</b> \'dd/mm/yyyy\'<br>" +
                    "<b>Inclusion:</b> included in the interval<br>" +
                    "<b>Default value if empty:</b> " +
                    this.defaultEndDate.toLocaleDateString() +"</div>",
           target: this._getEndDateField()
        });
    },
});
