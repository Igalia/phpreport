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
                                start.setMaxValue(date.add(Date.DAY, 1));
                                start.validate();
                                field.dateRangeMax = date;
                                end.dateRangeMax = date;
                                start.dateRangeMax = date;
                        }
                        // If we have an earlier max date
                        else if (field.dateRangeMax > date )
                        {
                                start.setMaxValue(date.add(Date.DAY, 1));
                                start.validate();
                                field.dateRangeMax = date;
                                end.dateRangeMax = date;
                                start.dateRangeMax = date;
                        }
                        // If this is the only end date specified (then it's the only
                        // upper limit)
                        else if (!end.getValue())
                        {
                                start.setMaxValue(date.add(Date.DAY, 1));
                                start.validate();
                                field.dateRangeMax = date;
                                end.dateRangeMax = date;
                                start.dateRangeMax = date;
                        }
                        // If this end date is earlier than the other
                        else if (date < end.getValue())
                        {
                                start.setMaxValue(date.add(Date.DAY, 1));
                                start.validate();
                                field.dateRangeMax = date;
                                end.dateRangeMax = date;
                                start.dateRangeMax = date;
                        }
                        // If this end date is later than the other
                        else if (field.dateRangeMax < end.getValue())
                        {
                                start.setMaxValue(end.getValue().add(Date.DAY, 1));
                                start.validate();
                                field.dateRangeMax = end.getValue();
                                end.dateRangeMax = end.getValue();
                                start.dateRangeMax = end.getValue();
                        }

        } else if (field.endDateField1 && field.endDateField1 && (!field.dateRangeMin || (date.getTime() != field.dateRangeMin.getTime()))) {
            var end1 = Ext.getCmp(field.endDateField1);
            var end2 = Ext.getCmp(field.endDateField2);
            end1.setMinValue(date.add(Date.DAY, -1));
            end2.setMinValue(date.add(Date.DAY, -1));
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
        if (field.endTimeField && (!field.timeRangeMax || (time.getTime() != field.timeRangeMax.getTime()))) {
            var start = field.parent.initTimeField;
            updateTimes(start, null, time);
            start.maxValue = time;
            start.validate();
            field.timeRangeMax = time;
        }
        else if (field.initTimeField && (!field.timeRangeMin || (time.getTime() != field.timeRangeMin.getTime()))) {
            var end = field.parent.endTimeField;
            updateTimes(end, time, null);
            end.minValue = time;
            end.validate();
            field.timeRangeMin = time;
        }
        /*
         * Always return true since we're only using this vtype to set the
         * min/max allowed values (these are tested for after the vtype test)
         */
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
            start.setMaxValue(date.add(Date.DAY, -1));
            start.validate();
            this.dateRangeMax = date;
        }
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            var end = Ext.getCmp(field.endDateField);
            end.setMinValue(date.add(Date.DAY, 1));
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
