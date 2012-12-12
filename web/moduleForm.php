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

define('PHPREPORT_ROOT', __DIR__ . '/../');

/* Get the needed variables to create the new Module */

$projectId = $_GET["pid"];

$moduleId = $_GET["mid"];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/ModuleVO.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/util/SQLUniqueViolationException.php');

if ($moduleId != NULL)
{
    $module = CoordinationFacade::GetModule($moduleId);
    define('PAGE_TITLE', "PhpReport - Update Module");
}
else
    define('PAGE_TITLE', "PhpReport - Create New Module");


/* Include the generic header and sidebar*/
include_once("include/header.php");
include_once("include/sidebar.php");


if ( (isset($_POST["name"])) && ( ($_POST["start"] != "") && ($_POST["name"] != "")) )
{

   if ($moduleId != NULL)
    {

        $module->setName($_POST["name"]);
        $module->setSummary($_POST["description"]);
        $module->setInit(date_create_from_format("d/m/Y", $_POST["start"]));
        if ($_POST["end"] != "")
                $module->setEnd(date_create_from_format("d/m/Y", $_POST["end"]));
        else
                $module->setEnd(NULL);
        try {
            CoordinationFacade::UpdateModule($module);
        } catch (SQLUniqueViolationException $e) {

            $uniqueError = true;

        }

    } else {
        $module = new ModuleVO();

        $module->setName($_POST["name"]);
        $module->setSummary($_POST["description"]);
        $module->setInit(date_create_from_format("d/m/Y", $_POST["start"]));
        if ($_POST["end"] != "")
                $module->setEnd(date_create_from_format("d/m/Y", $_POST["end"]));



        $module->setProjectId($projectId);

        try {
            CoordinationFacade::CreateModule($module);
        } catch (SQLUniqueViolationException $e) {

            $uniqueError = true;

        }
   }

}

?>

<script type="text/javascript">
// Add the additional VType to validate date inputs
Ext.apply(Ext.form.VTypes, {

    <?php

    if ($uniqueError)
        echo"
    duplicated : function(val, field) {

        if (val != '" . $_POST['name'] . "')
            return true;

        return false;

    },

            duplicatedText:\"An Module for Project " . $projectId . " with name '" . $_POST['name'] . "' already exists in DB\","; ?>
});

Ext.onReady(function(){
    // obtain the GET variables
    var urlVars = Ext.urlDecode(window.location.href.slice(window.location.href.indexOf('?') + 1));

    <?php if ((isset($_POST["name"])) && ($_POST["start"] != "") && ($_POST["name"] != ""))
            echo "window.location = 'viewModule.php?mid={$module->getId()}';";

    ?>

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    // enable validation error messages
    Ext.QuickTips.init();

    // define the form
    var moduleForm = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        standardSubmit: true,
        url:'moduleForm.php?' + Ext.urlEncode(urlVars),
        frame:true,
        title:
        <?php
           if ($moduleId == "")
                         echo "'Create New Module'";
           else
                         echo "'Update Module'";
        ?>,
        bodyStyle:'padding:5px 5px 0',
        width: 350,
              defaults: {width: 230,
              labelStyle: "text-align:right;"},
        defaultType: 'textfield',

        baseParams: urlVars,

        items: [{
            fieldLabel: 'Name <font color="red">*</font>',
            id : 'name',
            name: 'name',
            <?php
            if ($uniqueError)
                echo"vtype: 'duplicated',";
            ?>
            allowBlank:false,
            listeners: {
                'change': function() {
                    this.setValue(Trim(this.getValue()));
                }
            },
        },{
            fieldLabel: 'Description',
            id: 'description',
            name: 'description',
            xtype: 'textarea',
            listeners: {
                'change': function() {
                    this.setValue(Trim(this.getValue()));
                }
            },
        },{
            fieldLabel: 'Start Date <font color="red">*</font>',
            name: 'start',
            xtype: 'datefield',
            format: 'd/m/Y',
            startDay: 1,
            id: 'startDate',
            vtype:'daterange',
            allowBlank:false,
            endDateField: 'endDate' // id of the end date field
        },{
            fieldLabel: 'End Date',
            name: 'end',
            xtype: 'datefield',
            format: 'd/m/Y',
            startDay: 1,
            id: 'endDate',
            vtype:'daterange',
            startDateField: 'startDate' // id of the start date field
        },{
            xtype: 'label',
            html: '<font color="red">*</font> Required fields',
            style: 'padding: 5px 0 5px 10px'
        }],

        buttons: [{
            text: 'Save',
            handler: function(){
                    var fp = this.ownerCt.ownerCt,
                        form = fp.getForm();
                    if (form.isValid()) {
                        form.submit();
                    }
                }
            },{
            text: 'Cancel'
        }],
        });

    // render the form
    moduleForm.render(Ext.get("content"));

<?php

      // If there is already some module data, write it
        if (isset($module))
        {
            echo "var name = Ext.getCmp('name');
            name.setValue('" . $module->getName() . "');
            var description = Ext.getCmp('description');
            description.setValue('" . $module->getSummary() . "');
            var start = Ext.getCmp('startDate');
                    start.setRawValue('" . $module->getInit()->format('d/m/Y') . "');
                  start.validate();";
            if ($module->getEnd() != NULL)
                echo "var end = Ext.getCmp('endDate');
                        end.setRawValue('" . $module->getEnd()->format('d/m/Y') . "');
                    end.validate();";
        }
?>

});
</script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
