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

/* Get the needed variables to create the new Task Story*/

$taskStoryId = $_GET["tsid"];

$storyId = $_GET["stid"];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

include_once(PHPREPORT_ROOT . "/model/facade/UsersFacade.php");
include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskStoryVO.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/util/SQLUniqueViolationException.php');


if ($taskStoryId != NULL)
{
    $taskstory = CoordinationFacade::GetTaskStory($taskStoryId);
    $storyId = $taskstory->getStoryId();
    define('PAGE_TITLE', "PhpReport - Update Task Story");
}
else
    define('PAGE_TITLE', "PhpReport - Create New Task Story");


/* Include the generic header and sidebar*/
include_once("include/header.php");
include_once("include/sidebar.php");

if ( (isset($_POST["name"])) && ( ($_POST["name"] != "") && ($_POST["estHours"] != "") && ($_POST["startDate"] != "") && ($_POST["estEndDate"] != "") && ($_POST["hiddenDeveloper"] != "")))
{

   if ($taskStoryId != NULL)
    {

        $taskstory->setName($_POST["name"]);
        $taskstory->setRisk($_POST["hiddenRisk"]);
        $taskstory->setEstHours($_POST["estHours"]);
        $taskstory->setToDo($_POST["pendHours"]);

        if ($_POST["startDate"] != "")
            $taskstory->setInit(date_create_from_format("d/m/Y", $_POST["startDate"]));
        else
            $taskstory->setInit(NULL);

        if ($_POST["endDate"] != "")
            $taskstory->setEnd(date_create_from_format("d/m/Y", $_POST["endDate"]));
        else
            $taskstory->setEnd(NULL);

        if ($_POST["estEndDate"] != "")
            $taskstory->setEstEnd(date_create_from_format("d/m/Y", $_POST["estEndDate"]));
        else
            $taskstory->setEstEnd(NULL);

        $taskstory->setUserId($_POST["hiddenDeveloper"]);

        try {
            CoordinationFacade::UpdateTaskStory($taskstory);
        } catch (SQLUniqueViolationException $e) {

            $uniqueError = true;

        }

    } else {

        $taskstory = new TaskStoryVO();

        $taskstory->setName($_POST["name"]);
        $taskstory->setRisk($_POST["hiddenRisk"]);
        $taskstory->setEstHours($_POST["estHours"]);
        $taskstory->setToDo($_POST["pendHours"]);
        if ($_POST["startDate"] != "")
            $taskstory->setInit(date_create_from_format("d/m/Y", $_POST["startDate"]));
        if ($_POST["endDate"] != "")
            $taskstory->setEnd(date_create_from_format("d/m/Y", $_POST["endDate"]));
        if ($_POST["estEndDate"] != "")
            $taskstory->setEstEnd(date_create_from_format("d/m/Y", $_POST["estEndDate"]));
        $taskstory->setUserId($_POST["hiddenDeveloper"]);
        $taskstory->setStoryId($storyId);

        try {
            CoordinationFacade::CreateTaskStory($taskstory);
        } catch (SQLUniqueViolationException $e) {

            $uniqueError = true;

        }

    }

}

$users = UsersFacade::GetStoryIterationProjectAreaTodayUsers($storyId);

$taskSections = CoordinationFacade::GetStoryTaskSections($storyId);

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

            duplicatedText:\"A Task Story for Story " . $storyId . " with name '" . $_POST['name'] . "' already exists in DB\","; ?>
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

    var taskSectionsStore = new Ext.data.ArrayStore({
            id: 0,
            fields: ['id', 'name'],
            data : [
    <?php

        foreach((array)$taskSections as $taskSection)
            echo "[{$taskSection->getId()}, '{$taskSection->getName()}'],";

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
    var taskStoryForm = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        standardSubmit: true,
        url:'storyForm.php?' + Ext.urlEncode(urlVars),
        frame:true,
        title:
        <?php
           if ($taskStoryId == "")
                         echo "'Update Task Story'";
           else
                         echo "'Create New Task Story'";
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
            fieldLabel: 'Pending Hours',
            name: 'pendHours',
            id: 'pendHours',
            xtype: 'numberfield',
            allowBlank: true
        },{
            fieldLabel: 'Start Date',
            name: 'startDate',
            id: 'startDate',
            xtype: 'datefield',
            format: 'd/m/Y',
            vtype: 'doubledaterange',
              allowBlank: false,
            endDateField1: 'endDate',
            endDateField2: 'estEndDate' // id of the end date field
        },{
            fieldLabel: 'Estimated End Date',
            name: 'estEndDate',
            id: 'estEndDate',
            xtype: 'datefield',
            format: 'd/m/Y',
            vtype: 'doubledaterange',
              allowBlank: false,
            startDateField: 'startDate',
            endDateField: 'endDate' // id of the start date field
        },{
            fieldLabel: 'End Date',
            name: 'endDate',
            id: 'endDate',
            xtype: 'datefield',
            format: 'd/m/Y',
            vtype: 'doubledaterange',
              allowBlank: true,
            startDateField: 'startDate',
            endDateField: 'estEndDate' // id of the start date field
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

        },{
            fieldLabel: 'TaskSection',
            name: 'taskSection',
            id: 'taskSection',
            xtype: 'combo',
            forceSelection: true,
            allowBlank: false,
            displayField: 'name',
            valueField: 'id',
            hiddenName: 'hiddenTaskSection',
            store: taskSectionsStore,
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            emptyText:'Select a Task Section...',
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
            if (isset($taskstory))
            {
                echo "var name = Ext.getCmp('name');
                    name.setValue('" . $taskstory->getName() . "');
                    var risk = Ext.getCmp('risk');
                    risk.setValue('" . $taskstory->getRisk() . "');
                    var estHours = Ext.getCmp('estHours');
                    estHours.setValue('" . $taskstory->getEstHours() . "');
                    var startDate = Ext.getCmp('startDate');
                    startDate.setRawValue('" . $taskstory->getStart()->format('d/m/Y') . "');
                    startDate.validate();
                    var estEndDate = Ext.getCmp('estEndDate');
                    estEndDate.setRawValue('" . $taskstory->getEstEnd()->format('d/m/Y') . "');
                    estEndDate.validate();
                    var developer = Ext.getCmp('developer');
                    developer.setValue('" . $taskstory->getUserId() . "');
                    var pendHours = Ext.getCmp('pendHours');
                    pendHours.setValue('" . $taskstory->getToDo() . "');";
                if ($taskstory->getEnd() != NULL)
                    echo "var endDate = Ext.getCmp('endDate');
                        endDate.setRawValue('" . $taskstory->getEnd()->format('d/m/Y') . "');
                        endDate.validate();";
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
