<?php
/*
 * Copyright (C) 2009-2014 Igalia, S.L. <info@igalia.com>
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

    define('PHPREPORT_ROOT', __DIR__ . '/../');

    /* We check authentication and authorization */
    require_once(PHPREPORT_ROOT . '/web/auth.php');

    /* Include the generic header and sidebar*/
    define('PAGE_TITLE', "PhpReport - Working Hours Results Report");
    include_once("include/header.php");
?>
<script>
var loggedInUser = '<?php echo $_SESSION['user']->getLogin(); ?>';
</script>
<script src="js/include/sessionTracker.js"></script>
<script type="text/javascript" src="js/include/DateIntervalForm.js"></script>
<script type="text/javascript" src="js/include/ExportableGridPanel.js"></script>
<script type="text/javascript">

Ext.onReady(function(){

    // NOTE: This is an example showing simple state management. During development,
    // it is generally best to disable state management as dynamically-generated ids
    // can change across page loads, leading to unpredictable results.  The developer
    // should ensure that stable state ids are set for stateful components in real apps.
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

    // Variable for controlling the two XML stores loading (they must populate the main store when both have finished loading)
    var loaded = false;

    /**
     * Custom function used for column renderer
     * @param {Object} val
     */
    function renderHours(val) {
        return Ext.util.Format.number(val, '0,000.00');
    }

    /**
     * Custom function used for column renderer
     * @param {Object} val
     */
    function renderHolidayHours(val) {
        if (val < 0) {
            return '<span style="color:red;">' + Ext.util.Format.number(val, '0,000.00') + '</span>';
        }
        return Ext.util.Format.number(val, '0,000.00');
    }

    /**
     * Custom function used for column renderer
     * @param {Object} val
     */
    function renderExtraHours(val) {
        if (val > -50 && val < 50) {
            return '<span style="color:green;">' + Ext.util.Format.number(val, '0,000.00') + '</span>';
        } else {
            return '<span style="color:red;">' + Ext.util.Format.number(val, '0,000.00') + '</span>';
        }
    }


    var extraHours = new Ext.data.Store({

      url: 'services/getExtraHoursReportService.php?',
        reader: new Ext.data.XmlReader({
            record: 'report',
            idPath : '@login',
        }, [{name: 'login', mapping: '@login'},'extraHours', 'totalHours', 'workableHours', 'totalExtraHours', 'lastTaskDate'] )

    });



   var pendingHoliday = new Ext.data.Store({

        url: 'services/getPendingHolidayHoursService.php?',
        reader: new Ext.data.XmlReader({
            record: 'pendingHolidayHours',
            idPath : '@login',
        }, [{name: 'login', mapping: '@login'},'hours'] )

    });

     var store = new Ext.data.ArrayStore({

        fields: [
            {name: 'login'},
            {name: 'pendingHoliday', type: 'float'},
            {name: 'extraHours', type: 'float'},
            {name: 'workableHours', type: 'float'},
            {name: 'totalHours', type: 'float'},
            {name: 'totalExtraHours', type: 'float'},
            {name: 'lastTaskDate', type: 'date', dateFormat: 'Y-m-d'}]

    });


    var dataId = 0;

    function move(row) {

        var index = pendingHoliday.findExact('login', row.get('login'));

        var row2 = pendingHoliday.getAt(index);

        var data = {

            login: row.get('login'),
            pendingHoliday: parseFloat(row2.get('hours')),
            extraHours: parseFloat(row.get('extraHours')),
            totalHours: parseFloat (row.get('totalHours')),
            totalExtraHours: parseFloat(row.get('totalExtraHours')),
            workableHours: parseFloat(row.get('workableHours')),
            lastTaskDate: row.get('lastTaskDate')

        };

        var row = new store.recordType(data, dataId);

        dataId++;

        store.add([row]);

    }

    // Variable to store the login of the selected row between reloads of the grid
    var selectedLogin;

    // create the event handling function for populating the main store when both XML stores have loaded their data
    function populate() {

        if (loaded == true)
        {

            store.removeAll();
            loaded = false;
            extraHours.each(move);

            store.sort('login', 'ASC');
            if (!grid.rendered) {
                grid.render(Ext.get("content"));
                //Handler to set default row as logged in user
                grid.on('viewReady', function () {
                    selectRowForUser(loggedInUser);
                });
            } else {
                // select and scroll to previously selected row
                selectRowForUser(selectedLogin);

                //hide "loading"
                grid.customMask.hide();
            }

        } else loaded = true;
    }

    // Find the row corresponding to a certain user login, selects and scrolls to it
    function selectRowForUser(login) {
        var index = store.findExact('login', login);
        grid.getSelectionModel().selectRow(index);
        grid.getView().focusRow(index);
    }

    extraHours.on('load', populate);

    pendingHoliday.on('load', populate);

    // create the Grid
    var contentElement = document.getElementById('content');
    var grid = new Ext.ux.ExportableGridPanel({
        id: 'grid',
        store: store,
        columns: [
            {id: 'login', width: 130, header: 'Login', sortable: true, dataIndex: 'login'},
            {id: 'pendingHoliday', width: 130, header: 'Pending Holiday Hours', sortable: true, renderer: renderHolidayHours, dataIndex: 'pendingHoliday'},
            {id: 'extraHours', width: 130, header: 'Extra Hours', sortable: true, renderer: renderExtraHours, dataIndex: 'extraHours'},
            {id: 'workableHours', width: 130, header: 'Workable Hours', sortable: true, renderer: renderHours, dataIndex: 'workableHours'},
            {id: 'totalHours', width: 130, header: 'Worked Hours', sortable: true, renderer: renderHours, dataIndex: 'totalHours'},
            {id: 'totalExtraHours', width: 130, header: 'Total Extra Hours', sortable: true, renderer: renderExtraHours, dataIndex: 'totalExtraHours'},
            {id: 'lastTaskDate', width: 130, header: 'Last task date', sortable: true, xtype: 'datecolumn', format: 'd/m/Y', dataIndex: 'lastTaskDate'}
        ],
        stripeRows: true,
        height: window.innerHeight - contentElement.offsetTop - DATE_INTERVAL_FORM_HEIGHT - 10,
        width: '100%',
        title: 'Working Hours Results Report',
        // config options for stateful behavior
        stateful: true,
        stateId: 'grid',
        listeners: {
            render: function(){

              grid.customMask =  new Ext.LoadMask(grid.getGridEl(), {msg:"Loading...", removeMask: false});

            }
        }
    });


    // dates filter form
    var workingResultsForm = new Ext.ux.DateIntervalForm({
        renderTo: 'content',
        listeners: {
            'view': function (element, init, end) {

                // store selected row to restore it after load
                if (grid.getSelectionModel().getSelected() !== undefined)
                    selectedLogin = grid.getSelectionModel().getSelected().get('login');

                if (grid.rendered)
                    grid.customMask.show();

                pendingHoliday.removeAll();

                // change web services URLs with those values and load data
                pendingHoliday.proxy.conn.url= 'services/getPendingHolidayHoursService.php?<?php

                    if ($sid!="")
                        echo "&sid=" . $sid;

                ?>&init=' + init.getFullYear() + "-01-01&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();

                pendingHoliday.load();


                extraHours.removeAll();

                extraHours.proxy.conn.url= 'services/getExtraHoursReportService.php?<?php

                    if ($sid!="")
                        echo "&sid=" . $sid;

                ?>&init=' + init.getFullYear() + "-" + (init.getMonth()+1) + "-" + init.getDate()  + "&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();

                extraHours.load();

            }
        }
    });
    //explicitly set start date so it becomes visible
    var defaultStartDate = new Date();
    defaultStartDate.setMonth(0);
    defaultStartDate.setDate(1); //defaultStartDate is 1st Jan of current year
    workingResultsForm.setStartDate(defaultStartDate);
    workingResultsForm.focus(true);

});

</script>

<div id="content">
</div>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
