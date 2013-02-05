/*
 * Copyright (C) 2012, 2013 Igalia, S.L. <info@igalia.com>
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
 * @class Ext.ux.DateIntervalForm
 * @extends Ext.FormPanel
 */
Ext.ux.DateIntervalForm = Ext.extend(Ext.FormPanel, {

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
        var dateField = this.get('startDate');
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
        var dateField = this.get('endDate');
        // check if the field has values, and if it doesn't, create default ones
        if (dateField.getRawValue() == "")
            return this.defaultEndDate;
        else
            return dateField.getValue();
    },

    initComponent: function () {

        Ext.apply(this, {
            //default visual configuration
            frame: true,
            header: false,
            bodyStyle: 'padding:5px 5px 0',
            labelWidth: 75,
            width: 350,
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
                vtype: 'daterange',
                endDateField: 'endDate' // id of the end date field
            },{
                fieldLabel: 'End Date',
                name: 'end',
                xtype: 'datefield',
                format: 'd/m/Y',
                startDay: 1,
                id: 'endDate',
                vtype: 'daterange',
                startDateField: 'startDate' // id of the start date field
            }],

            //button: send form
            buttons: [{
                text: 'View',
                scope: this, //scope inside the handler will be the Form object
                handler: function () {
                    this.fireEvent("view", this,
                        this.getStartDate(), this.getEndDate());
                }
            }]
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
           target: this.get('startDate')
        });
        Ext.QuickTips.register({
           text: "<div align='justify'>" +
                    "<b>Format:</b> \'dd/mm/yyyy\'<br>" +
                    "<b>Inclusion:</b> included in the interval<br>" +
                    "<b>Default value if empty:</b> " +
                    this.defaultEndDate.toLocaleDateString() +"</div>",
           target: this.get('endDate')
        });
    },
});
