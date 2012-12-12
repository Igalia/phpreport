<?php
/*
 * Copyright (C) 2009-2012 Igalia, S.L. <info@igalia.com>
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
    define('PAGE_TITLE', "PhpReport - User Details");
    include_once("include/header.php");
    include_once("include/sidebar.php");
    include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/StoryVO.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

    $uid = $_GET['uid'];

    $userToShow = UsersFacade::GetUser($uid);

?>

<script type="text/javascript" src="js/include/DateIntervalForm.js"></script>
<script type="text/javascript">

    Ext.onReady(function(){


    // Main Panel
    var mainPanel = new Ext.FormPanel({
          width: 175,
        labelWidth: 50,
            frame:true,
        title: 'User Data',
            bodyStyle: 'padding:5px 5px 0px 5px;',
        defaults: {
                 width: 125,
                 labelStyle: 'text-align: right; width: 50; font-weight:bold; padding: 0 0 0 0;',
        },
        defaultType:'displayfield',
                items: [{
                        id:'login',
                        name: 'login',
                           fieldLabel:'Login',
                        <?php
                              echo "value:'" . $userToShow->getLogin() . "'";
                        ?>
                    },{
                        id:'groups',
                        name:'groups',
                        fieldLabel: 'Groups',
                        value: '-<?php

                            foreach((array)$userToShow->getGroups() as $group)
                                {
                                    echo " " . $group->getName() . " -";
                                }

                        ?>'
                    }
                ]
    });

    mainPanel.render(Ext.get("content"));
        Ext.ux.DynamicGridPanel = Ext.extend(Ext.grid.GridPanel, {

          initComponent: function(){
            /**
             * Default configuration options.
             *
             * You are free to change the values or add/remove options.
             * The important point is to define a data store with JsonReader
             * without configuration and columns with empty array. We are going
             * to setup our reader with the metaData information returned by the server.
             * See http://extjs.com/deploy/dev/docs/?class=Ext.data.JsonReader for more
             * information how to configure your JsonReader with metaData.
             *
             * A data store with remoteSort = true displays strange behaviours such as
             * not to display arrows when you sort the data and inconsistent ASC, DESC option.
             * Any suggestions are welcome
             */
            var config = {
              viewConfig: {forceFit: true},
              stateful: true,
              stateId: 'userProjectCustomerGrid',
              loadMask: true,
              stripeRows: true,
              ds: new Ext.data.Store({
                    url: this.storeUrl,
                    reader: new Ext.data.JsonReader()
              }),
              columns: []
            };

            Ext.apply(this, config);
            Ext.apply(this.initialConfig, config);

            // listener for double click to open the project details page
            this.on('rowdblclick', function(g, n) {
                window.location = 'viewProjectDetails.php?pid=' +
                        g.getStore().getAt(n).get('id');
            });

            Ext.ux.DynamicGridPanel.superclass.initComponent.apply(this, arguments);
          },

          onRender: function(ct, position){
            this.colModel.defaultSortable = true;

            Ext.ux.DynamicGridPanel.superclass.onRender.call(this, ct, position);

          }
        });

        var grid = new Ext.ux.DynamicGridPanel({
            id: 'my-grid',
            storeUrl: 'services/getUserProjectCustomerReportJsonService.php?<?php

                if ($sid!="")
                    echo "&sid=" . $sid;

                echo "&uid=" . $uid;?>',
            rowNumberer: false,
            checkboxSelModel: false,
            width: 1000,
            height: 250,
            frame: false,
            title: 'User Project-Customer Worked Hours Report',
            iconCls: 'icon-grid',
        });


            grid.store.on('load', function(){
              /**
               * Thats the magic!
               *
               * JSON data returned from server has the column definitions
               */
              if(typeof(grid.store.reader.jsonData.columns) === 'object') {
                var columns = [];

                  /**
                   * Adding RowNumberer or setting selection model as CheckboxSelectionModel
                   * We need to add them before other columns to display first
                   */
                if(grid.rowNumberer) { columns.push(new Ext.grid.RowNumberer()); }
                if(grid.checkboxSelModel) { columns.push(new Ext.grid.CheckboxSelectionModel()); }

                Ext.each(grid.store.reader.jsonData.columns, function(column){
                  columns.push(column);
                });

              /**
               * Setting column model configuration
               */
                grid.getColumnModel().setConfig(columns);

                if (!grid.rendered)
                    grid.render(Ext.get("content"));

              }

            }, this);

    // dates filter form
    var dates = new Ext.ux.DateIntervalForm({
        renderTo: 'content',
        listeners: {
            'view': function (element, init, end) {

                grid.store.proxy.conn.url= 'services/getUserProjectCustomerReportJsonService.php?<?php

                    if ($sid!="")
                        echo "&sid=" . $sid;

                                        echo "&uid=" . $uid;

                ?>&init=' + init.getFullYear() + "-" + (init.getMonth()+1) + "-" + init.getDate()  + "&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();

                grid.store.removeAll();
                grid.store.load();

            }
        }
    });

    grid.render(Ext.get('content'));
    grid.store.load();

    })

</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
