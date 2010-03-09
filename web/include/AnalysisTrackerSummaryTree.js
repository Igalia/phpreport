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

var AnalysisTrackerSummaryTree = Ext.extend(Ext.ux.tree.ColumnTree, {

    // these parameters are configurable
    projectId: "",
    projectName: "",
    user: "",
    moduleId: "",

    initComponent:function() {

        // configure widget
        Ext.apply(this, {
            collapsible: true,
            width: 950,
            height: 300,
            rootVisible:false,
            autoScroll:true,
            useArrows: true,
            title: 'Project: ' + this.projectName,
            iconCls: 'silk-sitemap-color',

            columns:[{
                header:'Task',
                width:330,
                dataIndex:'task',
                renderer: function(value, metaData, record, rowIndex, colIndex, store){
                    if (record.class != 'task-story') return value;
                    state = 'started-task';
                    if (!(record.spent > 0)) {
                        state = 'not-started-task';
                    } else if (!(record.toDo > 0)) {
                        state = 'finished-task';
                    }
                    return "<font class=" + state + ">" + value + "</font>";
                },
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
                header: 'Init Date',
                width:100,
                dataIndex: 'init',
                xtype: 'datecolumn',
                format: 'Y-m-d',
            },{
                header: 'End Date',
                width:100,
                dataIndex: 'end',
                xtype: 'datecolumn',
                format: 'Y-m-d',
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
                window.location = 'moduleForm.php?pid=' + this.projectId;
            },

            /**
             * onBrother
             */
            onBrother: function() {

                var node = this.getSelectionModel().getSelectedNode();

                if(node.attributes.class=='module')
                    window.location = 'moduleForm.php?pid=' + this.projectId;
                if(node.attributes.class=='section')
                    window.location = 'sectionForm.php?mid=' + node.parentNode.attributes.internalId;
                if(node.attributes.class=='task-section')
                    window.location = 'viewSection.php?scid=' + node.parentNode.attributes.internalId;
            },

            /**
             * onSon
             */
            onSon: function() {

                var node = this.getSelectionModel().getSelectedNode();

                if(node.attributes.class=='module')
                    window.location = 'sectionForm.php?mid=' + node.attributes.internalId;
                if(node.attributes.class=='section')
                    window.location = 'viewSection.php?scid=' + node.attributes.internalId;
            }

        });

        // build toolbars and buttons.
        this.bbar = this.buildBottomToolbar();

        // call parent
        AnalysisTrackerSummaryTree.superclass.initComponent.apply(this, arguments);

        // install event handler
        this.on('dblclick', function(n) {
            if(n.attributes.class=='module')
                window.location = 'viewModule.php?mid=' + n.attributes.internalId;
            if(n.attributes.class=='section')
                window.location = 'viewSection.php?scid=' + n.attributes.internalId;
            if(n.attributes.class=='task-section')
                window.location = 'viewSection.php?scid=' + n.parentNode.attributes.internalId;
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
            this.sonBtn.setDisabled((sm.getSelectedNode().attributes.class=='task-section'));
            this.brotherBtn.enable();
        }, this);

    },
});
