<?php
/*
 * Copyright (C) 2012 Igalia, S.L. <info@igalia.com>
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
 * System settings UI
 *
 * @filesource
 * @package PhpReport
 * @subpackage UI
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

define('PHPREPORT_ROOT', __DIR__ . '/../');

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');

/* There are POST data: we try to save the settings */
if(isset($_POST["numberOfDays"])) {
    $dayLimitEnabled = false;
    $dateLimitEnabled = false;
    $numberOfDays = null;
    $date = null;
    if(isset($_POST["dayLimitEnabled"])) {
        $dayLimitEnabled = true;
    }
    if(isset($_POST["dateLimitEnabled"])) {
        $dateLimitEnabled = true;
    }
    if(!empty($_POST["numberOfDays"])) {
        $numberOfDays = $_POST["numberOfDays"];
    }
    if(!empty($_POST["date"])) {
        $date = date_create($_POST["date"]);
    }
    $saved = TasksFacade::SetTaskBlockConfiguration($dayLimitEnabled, $numberOfDays,
            $dateLimitEnabled, $date);
}

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Settings");
include("include/header.php");

/* Read saved configuration */
$config = TasksFacade::GetTaskBlockConfiguration();

echo '<script>';
echo 'var dayLimitEnabled = ';
echo $config['dayLimitEnabled']?'true; ':'false; ';
echo 'var dateLimitEnabled = ';
echo $config['dateLimitEnabled']?'true; ':'false; ';
if($config['numberOfDays'] != null) {
    echo 'var numberOfDays = ' . $config['numberOfDays'] . ';';
}
else {
    echo 'var numberOfDays;';
}
if(!is_null($config['date'])) {
    echo "var date = Date.parseDate('" . $config['date']->format('Y-m-d') . "', 'Y-m-d');";
}
else {
    echo "var date = '';";
}
if (isset($saved)) {
    echo 'var saved = true; ';
    echo 'var errorOnSave = ';
    echo $saved? 'true; ': 'false; ';
}
else {
    echo 'var saved = false; ';
}
echo '</script>';

?>

<div id="content">
    <!-- Here we inject the form from the JavaScript code -->
</div>
<script>
Ext.onReady(function () {
    //show save results if any
    if(saved) {
        var App = new Ext.App({});
        if (errorOnSave) {
            App.setAlert(true, "Configuration correctly saved");
        }
        else {
            App.setAlert(false,
                    "Some error happened, configuration was not saved");
        }
    }

    var sendFormFunction = function () {
        form.getForm().getEl().dom.action = 'settings.php';
        form.getForm().getEl().dom.method = 'POST';
        form.getForm().submit();
    };

    var form = new Ext.form.FormPanel({
        standardSubmit: true,
        frame: true,
        title: 'Task report block settings',

        width: 250,
        defaults: {width: 130},
        items: [{
                fieldLabel: 'Day block enabled',
                xtype: 'checkbox',
                name: 'dayLimitEnabled',
                checked: dayLimitEnabled,
            },{
                fieldLabel: 'Number of days',
                xtype: 'numberfield',
                name: 'numberOfDays',
                value: numberOfDays,
            },{
                fieldLabel: 'Date block enabled',
                xtype: 'checkbox',
                name: 'dateLimitEnabled',
                checked: dateLimitEnabled,
            },{
                fieldLabel: 'Date',
                xtype: 'datefieldplus',
                format: 'd/m/Y',
                startDay: 1,
                useQuickTips: false,
                name: 'date',
                value: date,
            }
        ],
        buttons: [{
            text: 'Save',
            handler: sendFormFunction
        }],
        keys: [{
            key: [Ext.EventObject.ENTER],
            handler: sendFormFunction
        }]
    });

    form.render('content');
});
</script>

<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
