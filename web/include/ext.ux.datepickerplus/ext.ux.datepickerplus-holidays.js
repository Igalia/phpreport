/*
  * Ext.ux.DatePickerPlus  Addon
  * Ext.ux.form.DateFieldPlus  Addon
  *
  * This file overrides the default array of bank holidays shown on the calendar.
  *
  * @author    Marco Wienkoop (wm003/lubber)
  * @copyright (c) 2008, Marco Wienkoop (marco.wienkoop@lubber.de) http://www.lubber.de
  *
*/

if(Ext.ux.DatePickerPlus){
    Ext.apply(Ext.ux.DatePickerPlus.prototype, {
        nationalHolidays: function(year) {
            return [];
        }
    });

}
