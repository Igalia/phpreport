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

// Common validation functions

Ext.apply(Ext.form.VTypes, {

    // Validation for init date, end date and est end date
    doubledaterange : function(val, field) {
        var date = field.parseDate(val);

        if(!date){
            return;
        }
        if (field.startDateField && field.endDateField && (!field.dateRangeMax || (date.getTime() != field.dateRangeMax.getTime()))) {
                        var start = Ext.getCmp(field.startDateField);
                        var end = Ext.getCmp(field.endDateField);

                        // We check all the possibilities due to multiple data fields
                        // First of all, if we don't have a max date
                        if (!field.dateRangeMax)
                        {
                                start.setMaxValue(date);
                                start.validate();
                                field.dateRangeMax = date;
                                end.dateRangeMax = date;
                                start.dateRangeMax = date;
                        }
                        // If we have a later max date
                        else if (field.dateRangeMax > date )
                        {
                                start.setMaxValue(date);
                                start.validate();
                                field.dateRangeMax = date;
                                end.dateRangeMax = date;
                                start.dateRangeMax = date;
                        }
                        // If this is the only end date specified (then it's the only
                        // upper limit)
                        else if (!end.getValue())
                        {
                                start.setMaxValue(date);
                                start.validate();
                                field.dateRangeMax = date;
                                end.dateRangeMax = date;
                                start.dateRangeMax = date;
                        }
                        // If this end date is earlier than the other
                        else if (date < end.getValue())
                        {
                                start.setMaxValue(date);
                                start.validate();
                                field.dateRangeMax = date;
                                end.dateRangeMax = date;
                                start.dateRangeMax = date;
                        }
                        // If this end date is later than the other
                        else if (field.dateRangeMax < end.getValue())
                        {
                                start.setMaxValue(end.getValue());
                                start.validate();
                                field.dateRangeMax = end.getValue();
                                end.dateRangeMax = end.getValue();
                                start.dateRangeMax = end.getValue();
                        }

        } else if (field.endDateField1 && field.endDateField2 && (!field.dateRangeMin || (date.getTime() != field.dateRangeMin.getTime()))) {
            var end1 = Ext.getCmp(field.endDateField1);
            var end2 = Ext.getCmp(field.endDateField2);
            end1.setMinValue(date);
            end2.setMinValue(date);
            end1.validate();
            end2.validate();
            field.dateRangeMin = date;
            end1.dateRangeMin = date;
            end2.dateRangeMin = date;
        }
        /*
         * Always return true since we're only using this vtype to set the
         * min/max allowed values (these are tested for after the vtype test)
         */
        return true;
    },

    // Validation for init hour and end hour
    timerange : function(val, field) {
        var time = field.parseDate(val);

        if(!time){
            return;
        }
        if (field.endTimeField) {
            var start = field.parent.initTimeField;
            var time2 = start.parseDate(start.getValue());
            if (time2)
                if ((time <= time2) && (val != "00:00")) return false;
            if (val == "00:00")
                updateTimes(start, null, null);
            else updateTimes(start, null, time);
        }
        else if (field.initTimeField) {
            var end = field.parent.endTimeField;
            var time2 = end.parseDate(end.getValue());
            if (time2)
                if ((time2 <= time) && (end.getValue() != "00:00")) return false;
            updateTimes(end, time, null, open);
        }
        return true;
    },

    // Validation for init date and end date
    daterange : function(val, field) {
        var date = field.parseDate(val);

        if(!date){
            return;
        }
        if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            var start = Ext.getCmp(field.startDateField);
            start.setMaxValue(date);
            start.validate();
            this.dateRangeMax = date;
        }
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            var end = Ext.getCmp(field.endDateField);
            end.setMinValue(date);
            end.validate();
            this.dateRangeMin = date;
        }
        /*
         * Always return true since we're only using this vtype to set the
         * min/max allowed values (these are tested for after the vtype test)
         */
        return true;
    },

});
