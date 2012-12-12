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
?>

<script type="text/javascript">

var menuToolbar = new Ext.Toolbar({
        items: [{
            text: 'Tasks', handler: onItemClick,
            destination: "tasks.php",
        },{
            text: 'Coordination',
            menu: [
            {
                text: 'XP Tracker',
                handler: onItemClick,
                destination: "xptracker-summary.php",
                iconCls: 'silk-sitemap',
            },{
                text: 'Analysis Tracker',
                handler: onItemClick,
                destination: "analysistracker-summary.php",
                iconCls: 'silk-sitemap-color',
            }],
        },{
            text: 'Reports',
            menu: [
            {
                text: 'User tasks',
                handler: onItemClick,
                destination: "tasksFilter.php",
                iconCls: 'silk-pencil',
            },'-',
                <?php
                    if (isset($_SESSION['user'])){
                        $user = $_SESSION['user'];
                        echo "{ text: 'User details', ".
                            "handler: onItemClick, ".
                            "iconCls: 'silk-user-green', ".
                            "destination:'viewUserDetails.php?uid=" . $user->getId().
                            "',},";
                    }
                ?>
            {
                text: 'Users evaluation',
                handler: onItemClick,
                destination: 'usersEvaluation.php',
                iconCls: 'silk-user',
            },{
                text: 'Accumulated hours',
                handler: onItemClick,
                destination: 'viewWorkingHoursResultsReport.php',
                iconCls: 'silk-report-user',
            },'-',{
                text: 'Project evaluation',
                handler: onItemClick,
                destination: 'projectsEvaluation.php',
                iconCls: 'silk-book-open',
            },{
                text: 'Projects summary',
                handler: onItemClick,
                destination: 'projectsSummary.php',
                iconCls: 'silk-book',
            }],
        },{
            text: 'Data management',
            menu: [{
                text: 'Users',
                handler: onItemClick,
                destination: 'viewUsers.php',
                iconCls: 'silk-user-edit',
            },{
                text: 'Projects',
                handler: onItemClick,
                destination: 'viewProjects.php',
                iconCls: 'silk-book-edit',
            },{
                text: 'Clients',
                handler: onItemClick,
                destination: 'viewCustomers.php',
                iconCls: 'silk-vcard-edit',
            },{
                text: 'Areas',
                handler: onItemClick,
                destination: 'viewAreas.php',
                iconCls: 'silk-brick-edit',
            },{
                text: 'Cities',
                handler: onItemClick,
                destination: 'cityManagement.php',
                iconCls: 'silk-building-edit',
            },{
                text: 'Calendars',
                handler: onItemClick,
                destination: 'calendarManagement.php',
                iconCls: 'silk-calendar-edit'
            },{
                text: 'Hour compensations',
                handler: onItemClick,
                destination: 'hourCompensationManagement.php',
                iconCls: 'silk-script-edit'
            },{
                text: 'Application settings',
                handler: onItemClick,
                destination: 'settings.php',
                iconCls: 'silk-brick-edit'
            }],
        },
        new Ext.Toolbar.Fill(),
        {
            text: 'Help', handler: onItemClick,
            destination: "../help/user",
            newWindow: true
        },{
            text: 'Logout', handler: onItemClick,
            destination: "logout.php",
        }],
});

function onItemClick(item){
    if (item.destination) {
        if(item.newWindow) {
            window.open(item.destination);
        } else {
            window.location = item.destination;
        }
    } else {
        Ext.Msg.alert('Menu Click', 'The page "' + item.text +
                                    '" is not implemented yet.');
    }
}

Ext.onReady(function(){
    menuToolbar.render('menubar');
});

</script>

