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
<script src="js/include/sessionTracker.js"></script>
<script>
    var App = new Ext.App({});

    var cookieProvider = new Ext.state.CookieProvider({
        expires: new Date(new Date().getTime()+(1000*60*60*24*365)),
    });

    var templateRecord = new Ext.data.Record.create([
        {name:'id'},
        {name:'customerId'},
        {name:'projectId'},
        {name:'ttype'},
        {name:'story'},
        {name:'taskStoryId'},
        {name:'telework'},
        {name:'onsite'},
        {name:'text'},
        {name:'name'}
    ]);
    var store = new Ext.data.Store({
        autoSave: false,
        storeId: 'myStore',
        fields: templateRecord,
        reader: new Ext.data.XmlReader({
            record: 'template',
            successProperty: 'success',
            idProperty:'id'
        }, templateRecord),
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
            'exception': function(){
                App.setAlert(false, "Some error occurred");
                store.error = true;
            },
            'save': function (store, batch, data) {
                if(!store.error) {
                    App.setAlert(true, "Templates successfully migrated");
                    // workaround to remove the cookies, per https://www.sencha.com/forum/showthread.php?87334
                    Ext.util.Cookies.set('ys-taskTemplate', null, new Date("January 1, 1970"));
                    Ext.util.Cookies.clear('ys-taskTemplate');
                }
            }
        }
    });

    var templatesList = cookieProvider.decodeValue(
            cookieProvider.get('taskTemplate'));
    templatesList.forEach(function(t) {
        var newTemplate = new templateRecord();
        newTemplate.set('customerId', t[0]);
        newTemplate.set('projectId', t[1]);
        newTemplate.set('ttype', t[2]);
        newTemplate.set('story', t[3]);
        newTemplate.set('taskStoryId', t[4]);
        newTemplate.set('telework', t[5]);
        if (t.length == 7) {
            // it is a template from 2.0, without "onsite" field
            newTemplate.set('name', t[6]);
        }
        else {
            newTemplate.set('onsite', t[6]);
            newTemplate.set('name', t[7]);
        }

        store.add(newTemplate);
    });
    store.save();

</script>
