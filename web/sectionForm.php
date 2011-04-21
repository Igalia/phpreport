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

/* Get the needed variables to create the new Section*/

$moduleId = $_GET["mid"];

$sectionId = $_GET["scid"];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

include_once(PHPREPORT_ROOT . "/model/facade/UsersFacade.php");
include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/SectionVO.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/util/SQLUniqueViolationException.php');


if ($sectionId != NULL)
{
    $section = CoordinationFacade::GetSection($sectionId);
    define('PAGE_TITLE', "PhpReport - Update Section");
    $moduleId = $section->getModuleId();
}
else
    define('PAGE_TITLE', "PhpReport - Create New Section");


/* Include the generic header and sidebar*/
include_once("include/header.php");
include_once("include/sidebar.php");

if ( (isset($_POST["name"])) && ($_POST["name"] != "") && ($_POST["hiddenReviewer"] != "") ) {

   if ($sectionId != NULL)
    {

        $section->setName($_POST["name"]);
        $section->setAccepted($_POST["accepted"]);
        $section->setUserId($_POST["hiddenReviewer"]);
        $section->setText($_POST["text"]);

        try {
            CoordinationFacade::UpdateSection($section);
        } catch (SQLUniqueViolationException $e) {

            $uniqueError = true;

        }

    } else {

        $section = new SectionVO();

        $section->setName($_POST["name"]);
        $section->setAccepted($_POST["accepted"]);
        $section->setUserId($_POST["hiddenReviewer"]);
        $section->setModuleId($moduleId);
        $section->setText($_POST["text"]);

        try {
            CoordinationFacade::CreateSection($section);
        } catch (SQLUniqueViolationException $e) {

            $uniqueError = true;

        }
    }

}

$users = UsersFacade::GetModuleProjectAreaTodayUsers($moduleId);

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

            duplicatedText:\"A Section for Module " . $moduleId . " with name '" . $_POST['name'] . "' already exists in DB\","; ?>
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
        {
            echo "[{$auxUser->getId()}, '{$auxUser->getLogin()}'],";
            if ($auxUser->getId() == $_SESSION['user']->getId())
                $currentReviewer = true;
        }

        ?>]});


        // define the form
    var sectionForm = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        standardSubmit: true,
        url:'sectionForm.php?' + Ext.urlEncode(urlVars),
        frame:true,
        title:
        <?php
           if ($sectionId == "")
                         echo "'Create New Section'";
           else
                         echo "'Update Section'";
        ?>,
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {
            width: 230,
            labelStyle:"text-align:right"
        },
        defaultType: 'textfield',

        baseParams: urlVars,

        items: [{
            fieldLabel: 'Name <font color="red">*</font>',
            name: 'name',
            id: 'name',
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
            fieldLabel: 'Accepted',
            name: 'accepted',
            id: 'accepted',
            xtype: 'checkbox'
        },{
            fieldLabel: 'Text',
            id: 'text',
            name: 'text',
            xtype: 'textarea',
            listeners: {
                'change': function() {
                    this.setValue(Trim(this.getValue()));
                }
            },
        },{
            fieldLabel: 'Reviewer <font color="red">*</font>',
            name: 'reviewer',
            id: 'reviewer',
            xtype: 'combo',
            forceSelection: true,
            allowBlank:false,
            displayField: 'login',
            valueField: 'id',
            hiddenName: 'hiddenReviewer',
            store: usersStore,
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            emptyText:'Select a reviewer...',
            <?php
                if (($sectionId == "") && ($currentReviewer))
                         echo "value: {$_SESSION['user']->getId()},";
            ?>
            selectOnFocus:true
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
    sectionForm.render(Ext.get("content"));

<?php

      // If there was some error, rewrite all params introduced
            if (isset($section))
            {
                echo "var name = Ext.getCmp('name');
                    name.setValue('" . $section->getName() . "');
                    var text = Ext.getCmp('text');
                    text.setValue('" . $section->getText() . "');
                    var accepted = Ext.getCmp('accepted');
                    accepted.setValue('" . $section->getAccepted() . "');
                    var reviewer = Ext.getCmp('reviewer');
                    reviewer.setValue('" . $section->getUserId() . "');";
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
