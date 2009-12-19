var TrackerSummaryTree = Ext.extend(Ext.ux.tree.ColumnTree, {

    // these parameters are configurable
    projectId: "",
    projectName: "",
    user: "",
    iterationId: "",

    initComponent:function() {

        // configure widget
        Ext.apply(this, {
            collapsible: true,
            width: 750,
            height: 300,
            rootVisible:false,
            autoScroll:true,
            useArrows: true,
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
                    'iid':this.iterationId
                },
                dataUrl: 'services/getProjectTrackerTree.php',
                requestMethod: 'GET',
                preloadChildren: true,
                uiProviders:{
                    'col': Ext.ux.tree.ColumnNodeUI
                }
            }
            ),

            root: new Ext.tree.AsyncTreeNode({
                text:'Tasks', draggable:false,expanded:false, id:'root'
            }),

            /**
             * buildBottomToolbar
             */
            buildBottomToolbar : function() {
                return [{
                    text: 'Create Root',
                    id: this.id + 'RootBtn',
                    ref: '../rootBtn',
                    //iconCls: 'root-add',
                    handler: this.onRoot,
                    hidden: true,
                    scope: this
                    }, {
                    text: 'Create Brother',
                    id: this.id + 'BrotherBtn',
                    ref: '../brotherBtn',
                    disabled: true,
                    //iconCls: 'silk-arrow-down',
                    handler: this.onBrother,
                    scope: this
                    }, '-', {
                    text: 'Create Son',
                    id: this.id + 'SonBtn',
                    ref: '../sonBtn',
                    disabled: true,
                    //iconCls: 'silk-arrow-right',
                    handler: this.onSon,
                    scope: this
                }, '-'];
            },

            /**
             * onRoot
             */
            onRoot: function() {
                window.location = 'iterationForm.php?pid=' + this.projectId;
            },

            /**
             * onBrother
             */
            onBrother: function() {

                var node = this.getSelectionModel().getSelectedNode();

                if(node.attributes.class=='iteration')
                    window.location = 'iterationForm.php?pid=' + this.projectId;
                if(node.attributes.class=='story')
                    window.location = 'storyForm.php?iid=' + node.parentNode.attributes.internalId;
                if(node.attributes.class=='task-story')
                    window.location = 'viewStory.php?stid=' + node.parentNode.attributes.internalId;
            },

            /**
             * onSon
             */
            onSon: function() {

                var node = this.getSelectionModel().getSelectedNode();

                if(node.attributes.class=='iteration')
                    window.location = 'storyForm.php?iid=' + node.attributes.internalId;
                if(node.attributes.class=='story')
                    window.location = 'viewStory.php?stid=' + node.attributes.internalId;
            }

        });

        // build toolbars and buttons.
        this.bbar = this.buildBottomToolbar();

        // call parent
        TrackerSummaryTree.superclass.initComponent.apply(this, arguments);

        // install event handler
        this.on('dblclick', function(n) {
            if(n.attributes.class=='iteration')
                window.location = 'viewIteration.php?iid=' + n.attributes.internalId;
            if(n.attributes.class=='story')
                window.location = 'viewStory.php?stid=' + n.attributes.internalId;
            if(n.attributes.class=='task-story')
                window.location = 'viewStory.php?stid=' + n.parentNode.attributes.internalId;
        });

        this.on('load', function(node) {
                if(this.getRootNode().firstChild == null)
                {
                    this.getBottomToolbar().getComponent(0).show();
                    this.getBottomToolbar().getComponent(1).hide();
                    this.getBottomToolbar().getComponent(2).hide();
                    this.getBottomToolbar().getComponent(3).hide();
                }
                this.expandAll();
                //this.collapseAll();
        });

        // install event handler
        this.getSelectionModel().on('selectionchange', function(sm){
            this.sonBtn.setDisabled((sm.getSelectedNode().attributes.class=='task-story'));
            this.brotherBtn.enable();
        }, this);

    },
});
