<?php

/* Get the needed variables to create the new Iteration */

$projectId = $_GET["pid"];

$iterationId = $_GET["iid"];

/* We check authentication and authorization */
require_once('phpreport/web/auth.php');

include_once('phpreport/model/facade/CoordinationFacade.php');
include_once('phpreport/model/vo/IterationVO.php');
include_once('phpreport/web/services/WebServicesFunctions.php');
include_once('phpreport/util/SQLUniqueViolationException.php');

if ($iterationId != NULL)
{
    $iteration = CoordinationFacade::GetIteration($iterationId);
    define(PAGE_TITLE, "PhpReport - Update Iteration");
}
else
    define(PAGE_TITLE, "PhpReport - Create New Iteration");


/* Include the generic header and sidebar*/
include_once("include/header.php");
include_once("include/sidebar.php");


if ( (isset($_POST["name"])) && ( ($_POST["start"] != "") && ($_POST["name"] != "")) )
{

   if ($iterationId != NULL)
    {

        $iteration->setName($_POST["name"]);
        $iteration->setSummary($_POST["description"]);
        $iteration->setInit(date_create_from_format("d/m/Y", $_POST["start"]));
        if ($_POST["end"] != "")
                $iteration->setEnd(date_create_from_format("d/m/Y", $_POST["end"]));
        else
                $iteration->setEnd(NULL);
        try {
            CoordinationFacade::UpdateIteration($iteration);
        } catch (SQLUniqueViolationException $e) {

            $uniqueError = true;

        }

    } else {
        $iteration = new IterationVO();

        $iteration->setName($_POST["name"]);
        $iteration->setSummary($_POST["description"]);
        $iteration->setInit(date_create_from_format("d/m/Y", $_POST["start"]));
        if ($_POST["end"] != "")
                $iteration->setEnd(date_create_from_format("d/m/Y", $_POST["end"]));

        $iteration->setProjectId($projectId);

        try {
            CoordinationFacade::CreateIteration($iteration);
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

            duplicatedText:\"An Iteration for Project " . $projectId . " with name '" . $_POST['name'] . "' already exists in DB\","; ?>
});

Ext.onReady(function(){
    // obtain the GET variables
    var urlVars = Ext.urlDecode(window.location.href.slice(window.location.href.indexOf('?') + 1));

    <?php if ((isset($_POST["name"])) && ($_POST["start"] != "") && ($_POST["name"] != ""))
            echo "window.location = 'viewIteration.php?iid={$iteration->getId()}';";

    ?>

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    // enable validation error messages
    Ext.QuickTips.init();

    // define the form
    var iterationForm = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
                standardSubmit: true,
        url:'iterationForm.php?' + Ext.urlEncode(urlVars),
        frame:true,
        title:
        <?php
           if ($iterationId == "")
                         echo "'Create New Iteration'";
           else
                         echo "'Update Iteration'";
        ?>,
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {width: 230,
              labelStyle: "text-align: right;"
                },
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
            allowBlank:false
        },{
            fieldLabel: 'Description',
            id: 'description',
                        name: 'description',
            xtype: 'textarea'
        },{
            fieldLabel: 'Start Date <font color="red">*</font>',
            name: 'start',
            xtype: 'datefield',
            format: 'd/m/Y',
            id: 'startDate',
                        vtype:'daterange',
              allowBlank:false,
            endDateField: 'endDate' // id of the end date field
        },{
            fieldLabel: 'End Date',
            name: 'end',
            xtype: 'datefield',
            format: 'd/m/Y',
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
            text: 'Cancel',
            handler: function(){
                    <?php
                        if ($iterationId != NULL)
                            echo "window.location = 'viewIteration.php?iid={$iterationId}';";
                        else
                            echo "window.location = 'xptracker-summary.php';";
                    ?>
                }
        }],
        });

    // render the form
    iterationForm.render(Ext.get("content"));

<?php

      // If there is already some iteration data, write it
        if (isset($iteration))
        {
            echo "var name = Ext.getCmp('name');
            name.setValue('" . $iteration->getName() . "');
            var description = Ext.getCmp('description');
            description.setValue('" . $iteration->getSummary() . "');
            var start = Ext.getCmp('startDate');
                    start.setRawValue('" . $iteration->getInit()->format('d/m/Y') . "');
                    start.validate();";
            if ($iteration->getEnd() != NULL)
                echo "var end = Ext.getCmp('endDate');
                        end.setRawValue('" . $iteration->getEnd()->format('d/m/Y') . "');
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
