<?php
/*
 * Copyright (C) 2016 Igalia, S.L. <info@igalia.com>
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
define('PAGE_TITLE', "PhpReport - Template Migration");

define('PHPREPORT_ROOT', __DIR__ . '/../');

/* We check authentication */
require_once(PHPREPORT_ROOT . '/util/LoginManager.php');
if (!LoginManager::isLogged($sid))
    header('Location: login.php');

$userId = $_SESSION['user']->getId();

include(PHPREPORT_ROOT . 'web/include/header.php');
?>

<script>
    var App = new Ext.App({});

    var cookieProvider = new Ext.state.CookieProvider({
        expires: new Date(new Date().getTime()+(1000*60*60*24*365)),
    });

    var templateRecord = new Ext.data.Record.create([
        {name:'customerId'},
        {name:'projectId'},
        {name:'ttype'},
        {name:'story'},
        {name:'taskStoryId'},
        {name:'telework'},
        {name:'onsite'},
        {name:'name'}
    ]);
    var store = new Ext.data.ArrayStore({
        autoSave: false,
        storeId: 'myStore',
        fields: templateRecord,
        record: 'template',
        writer: new Ext.data.XmlWriter({
            xmlEncoding: 'UTF-8',
            root: 'templates',
            writeAllFields: true
        }, templateRecord),
        proxy: new Ext.data.HttpProxy({
            method: 'POST',
            url: 'services/createTemplatesService.php'
        }),
        listeners: {
            // this will always return an exception, because the configured reader
            // in this store does not understand the XML response from the service
            'exception': function(){
                App.setAlert(true, "Templates sent to server");
            }
        }
    });

    var templatesList = cookieProvider.decodeValue(
            cookieProvider.get('taskTemplate'));
    store.loadData(templatesList, true);
    store.save();

</script>
