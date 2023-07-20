<?php
/*
 * Copyright (C) 2009-2019 Igalia, S.L. <info@igalia.com>
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


/* Include the generic header */
define('PAGE_TITLE', "PhpReport - Login");

define('PHPREPORT_ROOT', __DIR__ . '/../');
include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

function set_location_header_on_login_success_url(){
    if (isset($_REQUEST["login_success_url"]) and !empty($_REQUEST["login_success_url"]))
        header("Location: " . $_REQUEST["login_success_url"]);
    else
        header("Location: tasks.php");
}

/* First check if a custom authentication header was set. */
if (strtolower(ConfigurationParametersManager::getParameter('USE_EXTERNAL_AUTHENTICATION')) === 'true') {
    LoginManager::login();
    set_location_header_on_login_success_url();
}
/* There are Http authentication data: we try to log in*/
else if (isset($_SERVER['PHP_AUTH_USER'])) {
    if (LoginManager::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))
        set_location_header_on_login_success_url();
    else
        echo _("Incorrect login information");
}
/* There are POST data: we try to log in */
else if (isset($_POST["login"]) && isset($_POST["password"])) {
    if (LoginManager::login($_POST["login"], $_POST["password"]))
        set_location_header_on_login_success_url();
    else
        echo _("Incorrect login information");
}
// If already logged in
else if (LoginManager::isLogged()) {
    set_location_header_on_login_success_url();
}
include("include/header.php");
?>

<div id="content" style="margin-left: 215px;">
    <!-- We inject the form here, from the JavaScript code -->
</div>

<script>
Ext.onReady(function(){

    var sendFormFunction = function () {
        form.getForm().getEl().dom.action = 'login.php';
        form.getForm().getEl().dom.method = 'POST';
        form.getForm().submit();
    };

    var form = new Ext.form.FormPanel({
        standardSubmit: true,
        frame: true,
        title: 'Login data for PhpReport',

        width: 350,
        defaults: {width: 230},
        defaultType: 'textfield',
        items: [{
                fieldLabel: 'Login',
                name: 'login',
                id: 'login',
                allowBlank:false
            },{
                inputType: 'password',
                fieldLabel: 'Password',
                name: 'password',
                allowBlank:false
            },{
                inputType: 'hidden',
                name: 'login_success_url',
                allowBlank:true,
                value: '<?php echo $_REQUEST['login_success_url'] ?? '' ?>'
            }
        ],
        buttons: [{
            text: 'Enter',
            handler: sendFormFunction
        }],
        keys: [{
            key: [Ext.EventObject.ENTER],
            handler: sendFormFunction
        }]
    });

    form.render('content');
    form.findById('login').focus();
});
</script>

<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
