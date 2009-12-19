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
