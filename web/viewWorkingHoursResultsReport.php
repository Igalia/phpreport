<?php
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


    /* We check authentication and authorization */
    require_once('phpreport/web/auth.php');

    /* Include the generic header and sidebar*/
    define('PAGE_TITLE', "PhpReport - Working Hours Results Report");
    include_once("include/header.php");
    include_once("include/sidebar.php");
    include_once('phpreport/model/facade/CoordinationFacade.php');
    include_once('phpreport/model/vo/StoryVO.php');
    include_once('phpreport/model/facade/UsersFacade.php');
    include_once('phpreport/web/services/WebServicesFunctions.php');


?>

<script type="text/javascript">

Ext.onReady(function(){

    // NOTE: This is an example showing simple state management. During development,
    // it is generally best to disable state management as dynamically-generated ids
    // can change across page loads, leading to unpredictable results.  The developer
    // should ensure that stable state ids are set for stateful components in real apps.
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

    Ext.QuickTips.init();

    // We initialize some variables
    var myData = [];

    var init = new Date(1900, 00, 01);

    // Variable for controlling the two XML stores loading (they must populate the main store when both have finished loading)
    var loaded = false;

    /**
     * Custom function used for column renderer
     * @param {Object} val
     */
    function hours(val){
        if(val > 0){
            return '<span style="color:green;">' + Ext.util.Format.number(val, '0,000.00') + '</span>';
        }else if(val < 0){
            return '<span style="color:red;">' + Ext.util.Format.number(val, '0,000.00') + '</span>';
        }
        return val;
    }


    var extraHours = new Ext.data.Store({

      url: 'services/getExtraHoursReportService.php?',
        reader: new Ext.data.XmlReader({
            record: 'report',
            idPath : '@login',
        }, [{name: 'login', mapping: '@login'},'extraHours', 'totalHours', 'workableHours', 'totalExtraHours'] )

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
            {name: 'totalExtraHours', type: 'float'}]

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
            workableHours: parseFloat(row.get('workableHours'))

        };

        var row = new store.recordType(data, dataId);

        dataId++;

        store.add([row]);

    }


    // create the event handling function for populating the main store when both XML stores have loaded their data
    function populate() {

        if (loaded == true)
        {

            store.removeAll();
            loaded = false;
            extraHours.each(move);

            store.sort('login', 'ASC');

            if (!grid.rendered)
                grid.render(Ext.get("content"));
            else grid.customMask.hide();

        } else loaded = true;

    }

    extraHours.on('load', populate);

    pendingHoliday.on('load', populate);

    // create the Grid
        var grid = new Ext.grid.GridPanel({
              id: 'grid',
              store: store,
        columns: [
            {id:'login',width: 70, header: 'Login',sortable: true, dataIndex: 'login'},
            {id: 'pendingHoliday', width: 130, header: 'Pending Holiday Hours', sortable: true, renderer: hours, dataIndex: 'pendingHoliday'},
            {id: 'extraHours', width: 100, header: 'Extra Hours', sortable: true, renderer: hours, dataIndex: 'extraHours'},
            {id: 'workableHours', width: 100, header: 'Workable Hours', sortable: true, renderer: hours, dataIndex: 'workableHours'},
            {id: 'totalHours', width: 100, header: 'Worked Hours', sortable: true, renderer: hours, dataIndex: 'totalHours'},
            {id: 'totalExtraHours', width: 130, header: 'Total Extra Hours', sortable: true, renderer: hours, dataIndex: 'totalExtraHours'}
        ],
        stripeRows: true,
        //autoExpandColumn: 'workableHours',
        //loadMask: true,
        height: 350,
        width: 600,
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


    // define the form
    var workingResultsForm = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        frame:true,
        title: 'Working Results',
        header: false,
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        renderTo: 'content',
        defaults: {width: 230},
        defaultType: 'datefield',
        items: [{
            fieldLabel: 'Start Date',
            name: 'start',
            xtype: 'datefield',
            format: 'd/m/Y',
            id: 'startDate',
            vtype:'daterange',
            endDateField: 'endDate' // id of the end date field
        },{
            fieldLabel: 'End Date',
            name: 'end',
            xtype: 'datefield',
            format: 'd/m/Y',
            id: 'endDate',
            vtype:'daterange',
            startDateField: 'startDate' // id of the start date field
        }],

        buttons: [{
            text: 'View',
            handler: function(){

                // check if the fields have values, and if they don't, create default ones
                if (Ext.getCmp('startDate').getRawValue() == "")
                    init = new Date(1900,00,01);
                else
                    init = Ext.getCmp('startDate').getValue();

                if (Ext.getCmp('endDate').getRawValue() == "")
                    end = new Date();
                else
                    end = Ext.getCmp('endDate').getValue();

                if (grid.rendered)
                                    grid.customMask.show();

                pendingHoliday.removeAll();

                // change web services URLs with those values and load data
                pendingHoliday.proxy.conn.url= 'services/getPendingHolidayHoursService.php?<?php

                    if ($sid!="")
                        echo "&sid=" . $sid;

                ?>&init=' + init.getFullYear() + "-01-01&end=" + end.getFullYear() + "-12-31";

                pendingHoliday.load();


                extraHours.removeAll();

                extraHours.proxy.conn.url= 'services/getExtraHoursReportService.php?<?php

                    if ($sid!="")
                        echo "&sid=" . $sid;

                ?>&init=' + init.getFullYear() + "-" + (init.getMonth()+1) + "-" + init.getDate()  + "&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();

                extraHours.load();

        }
        }],
    });

    Ext.QuickTips.register({
       text: "<div align='justify'><b>Format:</b> \'dd/mm/yyyy\'<br><b>Inclusion:</b> included in the interval<br><b>Default value (with \'\'):</b> 01/01/1900</div>",
       target: 'startDate'
    });

    var dateString = '', currentDate = new Date();

    if (currentDate.getDate() < 10)
        dateString += "0"
    dateString += currentDate.getDate() + "/";
    if (currentDate.getMonth() < 9)
        dateString += "0";
    dateString += (currentDate.getMonth() + 1) + "/";
    dateString += currentDate.getFullYear();

    Ext.QuickTips.register({
       text: "<div align='justify'><b>Format:</b> \'dd/mm/yyyy\'<br><b>Inclusion:</b> included in the interval<br><b>Default value (with \'\'):</b> " + dateString + " (current date)</div>",
       target: 'endDate'
    });


});

</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
