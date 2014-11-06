/*
 * Copyright (C) 2014 Igalia, S.L. <info@igalia.com>
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
 * Converts the data inside a store into a string in CSV format.
 * @param store with input data.
 * @return string in CSV format.
 */
function fromStoreToCSV(store) {
    var csv = "";

    // header with column names
    Ext.each(store.fields.items, function(item) {
        csv += item.name + ",";
    });
    csv += "\n";

    // data
    store.each(function (record) {
        Ext.each(store.fields.items, function(item) {
            csv += record.data[item.name] + ",";
        });
        csv += "\n";
    });

    return csv;
}

/**
 * @class Ext.ux.ExportableGridPanel
 * Grid with the ability to export its contents in CSV format. It adds a button
 * at the bottom bar to open the CSV data in a new browser window.
 * @extends Ext.GridPanel
 */
Ext.ux.ExportableGridPanel = Ext.extend(Ext.grid.GridPanel, {

    initComponent: function () {
        Ext.apply(this, {
            bbar: [
                {
                    xtype: 'button',
                    text: 'Download as CSV',
                    handler: function () {
                        //FIXME there must be a better way to get the grid
                        var gridComponent = this.ownerCt.ownerCt;
                        window.open("data:text/plain," + encodeURI(
                                fromStoreToCSV(gridComponent.getStore())));
                    }
                },
            ],
        });

        /* call the superclass to preserve base class functionality */
        Ext.ux.ExportableGridPanel.superclass.initComponent.apply(this, arguments);
    },

});
