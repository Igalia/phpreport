
<?php

    $pid = $_GET['pid'];

    /* We check authentication and authorization */
    require_once('phpreport/web/auth.php');

    /* Include the generic header and sidebar*/
    define(PAGE_TITLE, "PhpReport - Project Details");
    include_once("include/header.php");
    include_once("include/sidebar.php");
    include_once('phpreport/model/facade/CoordinationFacade.php');
    include_once('phpreport/model/vo/StoryVO.php');
    include_once('phpreport/model/facade/UsersFacade.php');
    include_once('phpreport/model/facade/ProjectsFacade.php');
    include_once('phpreport/web/services/WebServicesFunctions.php');

    $project = ProjectsFacade::GetProject($pid);

    $extraData = ProjectsFacade::GetProjectExtraData($pid);


?>

<script type="text/javascript">

    Ext.onReady(function(){

    // Main Panel
    var mainPanel = new Ext.FormPanel({
      autoWidth: true,
        labelWidth: 125,
            frame:true,
            layout: 'table',
            layoutConfig:{
                columns: 2
            },
        title: 'Project Data',
            bodyStyle: 'padding:5px 5px 0px 5px;',
        defaults: {
                 autoWidth: true,
                 labelStyle: 'text-align: right; width: 125; font-weight:bold; padding: 0 0 0 0;',
         colspan: 2,
        },
        defaultType: 'fieldset',
            items:[{
                xtype: 'fieldset',
              columnWidth: 0.5,
                title: 'Basic Data',
                collapsible: true,
                autoHeight: true,
                defaults: {
                     width: 200,
                     labelStyle: 'width: 125; font-weight:bold; padding: 0 0 0 0;'
                },
                defaultType:'displayfield',
                items: [{
                        id:'name',
                        name: 'name',
                           fieldLabel:'Name',
                        <?php
                              echo "value:'" . $project->getDescription() . "'";
                        ?>
                    },{
                        id:'id',
                        name: 'id',
                           fieldLabel:'Id',
                        <?php
                              echo "value:'" . $project->getId() . "'";
                        ?>
                    },{
                        id:'active',
                        name:'active',
                        fieldLabel: 'Active',
                        <?php

                            $active = $project->getActivation();
                            if (isset($active))
                            {
                                if ($active == False)
                                    echo "value:'No'";
                                else
                                    echo "value:'Yes'";
                            }

                        ?>
                    },{
                        id: 'estHours',
                        name:'estHours',
                        fieldLabel: 'Estimated Hours',
                        <?php

                            echo "value:'" . $project->getEstHours() . "'";

                        ?>
                    },{
                        id: 'invoice',
                        name:'invoice',
                        fieldLabel: 'Invoice',
                        <?php

                            echo "value:'" . $project->getInvoice() . "'";

                        ?>
                    },{
                        id: 'estHourCost',
                        name:'estHourCost',
                        fieldLabel: 'Estimated Hour Cost',
                        <?php

                            echo "value:'" . round($project->getInvoice()/$project->getEstHours(), 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    },{
                        id: 'Type',
                        name:'Type',
                        fieldLabel: 'Type',
                        <?php

                            echo "value:'" . $project->getType() . "'";

                        ?>
                    }
                ]
            },{
                xtype: 'fieldset',
        columnWidth: 0.5,
                title: 'Current Work Data',
                collapsible: true,
                colspan: 1,
                autoHeight: true,
                defaults: {
                     width: 70,
                     labelStyle: 'width: 125; font-weight:bold; padding: 0 0 0 0;'
                },
                defaultType:'displayfield',
                items: [{
                      id: 'workedHours',
                        name:'workedHours',
                        fieldLabel: 'Worked Hours',
                        <?php

                            echo "value:'" . $extraData[total] . "'";

                        ?>
                    },{
                        id: 'workDeviation',
                        name:'workDeviation',
                        fieldLabel: 'Work Deviation',
                        <?php

                            echo "value:'" . round($extraData[total]-$project->getEstHours(), 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    },{
                        id: 'workDeviationPercent',
                        name:'workDeviationPercent',
                        fieldLabel: 'Work Deviation %',
                        <?php

                            echo "value:'" . round(100*($extraData[total]-$project->getEstHours())/$extraData[total], 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    }
                ]
            },{
                xtype: 'fieldset',
                columnWidth: 0.5,
                title: 'Current Invoice Data',
                collapsible: true,
                colspan:1,
                autoHeight: true,
                defaults: {
                     width: 70,
                     labelStyle: 'text-align: right; width: 125; font-weight:bold; padding: 0 0 0 0;'
                },
                defaultType:'displayfield',
                items: [{
                        id: 'currentInvoice',
                        name:'currentInvoice',
                        fieldLabel: 'Current Invoice',
                        <?php

                            echo "value:'" . round($extraData[currentInvoice], 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    },{
                        id: 'invoiceDeviation',
                        name:'invoiceDeviation',
                        fieldLabel: 'Invoice Deviation',
                        <?php

                            echo "value:'" . round($extraData[currentInvoice]-($project->getInvoice()/$project->getEstHours()), 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    },{
                        id: 'invoiceDeviationPercent',
                        name:'invoiceDeviationPercent',
                        fieldLabel: 'Invoice Deviation %',
                        <?php

                            echo "value:'" . round((100*($extraData[currentInvoice]-($project->getInvoice()/$project->getEstHours()))/($project->getInvoice()/$project->getEstHours())), 2, PHP_ROUND_HALF_DOWN) . "'";

                        ?>
                    }
                ]
            }]
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
              stateId: 'projectUserCustomerGrid',
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

            Ext.ux.DynamicGridPanel.superclass.initComponent.apply(this, arguments);
          },

          onRender: function(ct, position){
            this.colModel.defaultSortable = true;

            Ext.ux.DynamicGridPanel.superclass.onRender.call(this, ct, position);

          }
        });

        var grid = new Ext.ux.DynamicGridPanel({
            id: 'my-grid',
            storeUrl: 'services/getProjectUserCustomerReportJsonService.php?<?php

                echo "login=" . $login;

                if ($sid!="")
                    echo "&sid=" . $sid;

                echo "&pid=" . $pid;?>',
            rowNumberer: false,
            checkboxSelModel: false,
            width: 400,
            height: 250,
            frame: false,
            title: 'Project Worked Hours Report',
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


                grid.store.proxy.conn.url= 'services/getProjectUserCustomerReportJsonService.php?<?php

                    echo "login=" . $login;

                    if ($sid!="")
                        echo "&sid=" . $sid;

                                        echo "&pid=" . $pid;

                ?>&init=' + init.getFullYear() + "-" + (init.getMonth()+1) + "-" + init.getDate()  + "&end=" + end.getFullYear() + "-" + (end.getMonth() + 1) + "-" + end.getDate();

                grid.store.load();

        }
        }],
        });

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
