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

var menuPanel = new Ext.Panel({
    frame: true,
    collapsible: true,
    title: 'Main menu',
    width: 125,
    items: [{
        border: false,
        xtype: 'buttongroup',
        columns: 1,
        defaults:{width:100},
        items: [{
            text: 'Tasks', handler: onItemClick,
            destination: "tasks.php",
        },{
            text: 'Coordination',
            menu: [
            {
                text: 'XP Tracker', handler: onItemClick, destination: "xptracker-summary.php", iconCls: 'silk-sitemap',
            },{
                text: 'Analysis Tracker', handler: onItemClick, destination: "analysistracker-summary.php", iconCls: 'silk-sitemap-color',
            },{
                text: 'My agenda', handler: onItemClick,
            },{
                text: 'Work calendar', handler: onItemClick,
            },{
                text: 'Project Schedule', handler: onItemClick,
            }],
        },{
            text: 'Users',
                menu: [
                <?php
                    if (isset($_SESSION['user'])){
                        $user = $_SESSION['user'];
                        echo "{ text: 'Personal page', handler: onItemClick, iconCls: 'silk-user-green', destination:'viewUserDetails.php?uid=" . $user->getId() . "',},";
                    }
                ?>
              {
                text: 'Users evaluation', handler: onItemClick, destination: 'usersEvaluation.php', iconCls: 'silk-user',
            },{
                text: 'Acc hours report', handler: onItemClick, destination: 'viewWorkingHoursResultsReport.php', iconCls: 'silk-report-user',
            },{
                text: 'Users management', handler: onItemClick, destination: 'viewUsers.php', iconCls: 'silk-user-edit',
            }],
        },{
            text: 'Projects',
            menu: [{
                text: 'Project evaluation', handler: onItemClick, destination: 'projectsEvaluation.php', iconCls: 'silk-book-open',
            },{
                text: 'Projects summary', handler: onItemClick, destination: 'projectsSummary.php', iconCls: 'silk-book',
            },{
                text: 'Management indexes', handler: onItemClick,
            },{
                text: 'Projects management', handler: onItemClick, destination: 'viewProjects.php', iconCls: 'silk-book-edit',
            }],
        },{
            text: 'Clients',
            menu: [{
                text: 'Clients summary', handler: onItemClick,
            },{
                text: 'Clients management', handler: onItemClick, destination: 'viewCustomers.php', iconCls: 'silk-vcard-edit',
            }],
        },{
            text: 'Areas',
            menu: [{
                text: 'Areas evaluation', handler: onItemClick,
            },{
                text: 'Turnover goals', handler: onItemClick,
            },{
                text: 'Areas management', handler: onItemClick, destination: 'viewAreas.php', iconCls: 'silk-brick-edit',
            }],
        }],
    }]
});

function onItemClick(item){
    if (item.destination) {
        window.location = item.destination;
    } else {
        Ext.Msg.alert('Menu Click', 'The page "' + item.text +
                                    '" is not implemented yet.');
    }
}

Ext.onReady(function(){
    menuPanel.render(Ext.get("sidebar"));
});

</script>

<div id="sidebar">
</div>
