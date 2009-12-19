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
                text: 'XP Tracker', handler: onItemClick,
                destination: "xptracker-summary.php",
            },{
                text: 'Analysis Tracker', handler: onItemClick,
                destination: "analysistracker-summary.php",
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
                text: 'User evaluation', handler: onItemClick, iconCls: 'silk-user',
            },{
                text: 'Acc hours report', handler: onItemClick, destination: 'viewWorkingHoursResultsReport.php', iconCls: 'silk-time',
            },{
                text: 'Users management', handler: onItemClick, destination: 'viewUsers.php', iconCls: 'silk-user-edit',
            }],
        },{
            text: 'Projects',
            menu: [{
                text: 'Project evaluation', handler: onItemClick,
            },{
                text: 'Projects summary', handler: onItemClick,
            },{
                text: 'Management indexes', handler: onItemClick,
            },{
                text: 'Projects management', handler: onItemClick,
            }],
        },{
            text: 'Clients',
            menu: [{
                text: 'Clients summary', handler: onItemClick,
            },{
                text: 'Clients management', handler: onItemClick,
            }],
        },{
            text: 'Areas',
            menu: [{
                text: 'Areas evaluation', handler: onItemClick,
            },{
                text: 'Turnover goals', handler: onItemClick,
            },{
                text: 'Areas management', handler: onItemClick,
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
