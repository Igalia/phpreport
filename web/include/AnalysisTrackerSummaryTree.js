var TrackerSummaryTree = Ext.extend(Ext.ux.tree.ColumnTree, {

    // these parameters are configurable
    projectId: "",
    projectName: "",
    user: "",
    moduleId: "",

    initComponent:function() {

        // configure widget
        Ext.apply(this, {
            collapsible: true,
            width: 750,
            height: 300,
            rootVisible:false,
            autoScroll:true,
            title: 'Project: ' + this.projectName,

            columns:[{
                header:'Task',
                width:330,
                dataIndex:'task'
            },{
                header:'Estimated',
                width:100,
                dataIndex:'duration'
            },{
                header:'Spent',
                width:100,
                dataIndex:'spent'
            },{
                header:'To do',
                width:100,
                dataIndex:'toDo'
            },{
                header:'Assigned To',
                width:100,
                dataIndex:'user'
            }],

            loader: new Ext.tree.TreeLoader({
                baseParams: {
                    'pid': this.projectId,
                    'login': this.user,
                    'mid':this.moduleId
                },
                dataUrl: 'services/getProjectAnalysisTrackerTree.php',
                requestMethod: 'GET',
                preloadChildren: true,
                uiProviders:{
                    'col': Ext.ux.tree.ColumnNodeUI
                }
            }
            ),

            root: new Ext.tree.AsyncTreeNode({
text:'Tasks', draggable:false,expanded:false, id:'root'
            })
        });

        // call parent
        TrackerSummaryTree.superclass.initComponent.apply(this, arguments);

        // install event handler
        this.on('click', function(n) {
            if(n.attributes.class=='module')
                window.location = 'viewModule.php?mid=' + n.attributes.internalId
                                                   '&login=' + this.user;
            if(n.attributes.class=='section')
                window.location = 'viewSection.php?scid=' + n.attributes.internalId
                                               '&login=' + this.user;
            if(n.attributes.class=='task-section')
                window.location = 'viewSection.php?scid=' + n.parentNode.attributes.internalId
                                               '&login=' + this.user;
        });
    },
});
