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

/* Get the needed variables to create the new Task Section*/

$taskSectionId = $_GET["tscid"];

$sectionId = $_GET["scid"];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

include_once(PHPREPORT_ROOT . "/model/facade/UsersFacade.php");
include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskSectionVO.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/util/SQLUniqueViolationException.php');


if ($taskSectionId != NULL)
{
    $tasksection = CoordinationFacade::GetTaskSection($taskSectionId);
    $sectionId = $tasksection->getSectionId();
    define('PAGE_TITLE', "PhpReport - Update Task Section");
}
else
    define('PAGE_TITLE', "PhpReport - Create New Task Section");


/* Include the generic header and sidebar*/
include_once("include/header.php");
include_once("include/sidebar.php");

if ( (isset($_POST["name"])) && ( ($_POST["name"] != "") && ($_POST["estHours"] != "") && ($_POST["hiddenDeveloper"] != "")))
{

   if ($taskSectionId != NULL)
    {

        $tasksection->setName($_POST["name"]);
        $tasksection->setRisk($_POST["hiddenRisk"]);
        $tasksection->setEstHours($_POST["estHours"]);
        $tasksection->setUserId($_POST["hiddenDeveloper"]);

        try {
            CoordinationFacade::UpdateTaskSection($tasksection);
        } catch (SQLUniqueViolationException $e) {

            $uniqueError = true;

        }

    } else {

        $tasksection = new TaskSectionVO();

        $tasksection->setName($_POST["name"]);
        $tasksection->setRisk($_POST["hiddenRisk"]);
        $tasksection->setEstHours($_POST["estHours"]);
        $tasksection->setUserId($_POST["hiddenDeveloper"]);
            $tasksection->setSectionId($sectionId);

        try {
            CoordinationFacade::CreateTaskSection($tasksection);
        } catch (SQLUniqueViolationException $e) {

            $uniqueError = true;

        }

    }

}

$users = UsersFacade::GetSectionModuleProjectAreaTodayUsers($sectionId);

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

            duplicatedText:\"A Task Section for Section " . $sectionId . " with name '" . $_POST['name'] . "' already exists in DB\","; ?>
});

Ext.onReady(function(){
    // obtain the GET variables
    var urlVars = Ext.urlDecode(window.location.href.slice(window.location.href.indexOf('?') + 1));

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    // enable validation error messages
    Ext.QuickTips.init();

    var usersStore = new Ext.data.ArrayStore({
            id: 0,
            fields: ['id', 'login'],
            data : [
    <?php

        foreach((array)$users as $auxUser)
            echo "[{$auxUser->getId()}, '{$auxUser->getLogin()}'],";

        ?>]});

    var riskStore = new Ext.data.ArrayStore({
            id: 0,
            fields: ['value', 'text'],
            data : [['0', 'None'],
                ['1', 'Minimum'],
                ['2', 'Low'],
                ['3', 'Medium'],
                ['4', 'High'],
                ['5', 'Critical']
            ]});


        // define the form
    var iterationForm = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        standardSubmit: true,
        url:'sectionForm.php?' + Ext.urlEncode(urlVars),
        frame:true,
        title:
        <?php
           if ($taskSectionId != NULL)
                         echo "'Update Task Section'";
           else
                         echo "'Create New Task Section'";
        ?>,
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {width: 230},
        defaultType: 'textfield',

        baseParams: urlVars,

        items: [{
            fieldLabel: 'Name',
            name: 'name',
            id: 'name',
        <?php
            if ($uniqueError)
                echo"vtype: 'duplicated',";
        ?>
            allowBlank:false
        },{
            fieldLabel: 'Risk',
            name: 'risk',
            id: 'risk',
            xtype: 'combo',
            allowBlank: true,
            displayField: 'text',
            valueField: 'value',
            hiddenName: 'hiddenRisk',
            store: riskStore,
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            emptyText:'Risk',
            selectOnFocus:true
        },{
            fieldLabel: 'Estimated Hours',
            name: 'estHours',
            id: 'estHours',
            xtype: 'numberfield',
            allowBlank: false
        },{
            fieldLabel: 'Developer',
            name: 'developer',
            id: 'developer',
            xtype: 'combo',
            forceSelection: true,
            allowBlank: false,
            displayField: 'login',
            valueField: 'id',
            hiddenName: 'hiddenDeveloper',
            store: usersStore,
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            emptyText:'Select a developer...',
            selectOnFocus:true

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
    iterationForm.render(Ext.get("content"));

<?php

      // If there was some error, rewrite all params introduced
            if (isset($tasksection))
            {
                echo "var name = Ext.getCmp('name');
                    name.setValue('" . $tasksection->getName() . "');
                    var risk = Ext.getCmp('risk');
                    risk.setValue('" . $tasksection->getRisk() . "');
                    var estHours = Ext.getCmp('estHours');
                    estHours.setValue('" . $tasksection->getEstHours() . "');
                    var developer = Ext.getCmp('developer');
                    developer.setValue('" . $tasksection->getUserId() . "';";
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
