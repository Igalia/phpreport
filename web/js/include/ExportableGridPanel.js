/*
 * Copyright (C) 2014, 2017 Igalia, S.L. <info@igalia.com>
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
 * Checks column data for visibility info; hidden columns will not be exported.
 * @param store with input data.
 * @param columnModel with column data.
 * @return string in CSV format.
 */
function fromStoreToCSV(store, columnModel) {
    var csv = "";

    var columns = columnModel.getColumnsBy(function (c) {
        return !c.hidden;
    });

    // header with column names
    Ext.each(columns, function(column) {
        csv += column.header + ",";
    });
    csv += "\n";

    // data
    store.each(function (record) {
        Ext.each(columns, function(column) {
            var item = store.fields.get(column.dataIndex);
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
                        const linkSource = `data:text/csv;charset=UTF-8,${encodeURIComponent(
                            fromStoreToCSV(gridComponent.getStore(), gridComponent.getColumnModel()))}`;
                        const downloadLink = document.createElement("a");
                        downloadLink.href = linkSource;
                        downloadLink.download = `${this.ownerCt.ownerCt.title.replace(/ /g, '')}.csv`;
                        downloadLink.click();
                    }
                },
            ],
        });

        /* call the superclass to preserve base class functionality */
        Ext.ux.ExportableGridPanel.superclass.initComponent.apply(this, arguments);
    },

});
