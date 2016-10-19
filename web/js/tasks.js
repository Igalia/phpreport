/*
 * Copyright (C) 2009-2015 Igalia, S.L. <info@igalia.com>
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

function updateTimes(field, min, max, open) {
    if(min == null){
        min = field.parseDate('00:00');
    }
    if(max == null){
        max = field.parseDate('23:59');
    }
    var times = [];
    min = min.add('mi', field.increment);
    while(min < max){
        times.push([min.dateFormat(field.format)]);
        min = min.add('mi', field.increment);
    }
    if (open)
        times.push(['00:00']);
    field.store.loadData(times);
};

var App = new Ext.App({});

/* Cookie provider */
var cookieProvider = new Ext.state.CookieProvider({
    expires: new Date(new Date().getTime()+(1000*60*60*24*365)),
});

/* Schema of the information about tasks */
var taskRecord = new Ext.data.Record.create([
    {name:'id'},
    {name:'date'},
    {name:'initTime'},
    {name:'endTime'},
    {name:'story'},
    {name:'telework'},
    {name:'onsite'},
    {name:'ttype'},
    {name:'text'},
    {name:'phase'},
    {name:'userId'},
    {name:'projectId'},
    {name:'taskStoryId'}
]);

/* Schema of the information about customers */
var customerRecord = new Ext.data.Record.create([
    {name:'id'},
    {name:'name'},
]);
/* Schema of the information about projects */
var projectRecord = new Ext.data.Record.create([
    {name:'id'},
    {name:'description'},
    {name:'customerName'}
]);
/* Schema of the information about task-stories */
var taskStoryRecord = new Ext.data.Record.create([
    {name:'id'},
    {name:'friendlyName'},
]);
/* Schema of the information of the personal summary */
var summaryRecord = new Ext.data.Record.create([
    {name:'day'},
    {name:'month'},
    {name:'week'},
    {name:'weekly_goal'},

]);
/* Schema of the information about task templates */
var templateRecord = new Ext.data.Record.create([
    {name:'id'},
    {name:'projectId'},
    {name:'ttype'},
    {name:'story'},
    {name:'taskStoryId'},
    {name:'telework'},
    {name:'onsite'},
    {name:'text'},
    {name:'name'}
]);
/* Variable to store if all tasks has loaded completely for a day */
var loaded = false;

/* Variable that will hold a fresh created task on page load */
var freshCreatedTaskRecord = false;

/* Variable that will hold a fresh panel, once created on page load */
var freshCreatedTaskPanel = false;
/**
 * Checks if the tasks for the day has loaded completely.
 *
 * @returns {boolean}
 */
function isLoaded() {
    return loaded;
}

/**
 * Check if a task is modified
 *
 * @param taskRecord
 * @returns {boolean}
 */
function isUnTouched(taskRecord) {
    if(!taskRecord.get('text') && !taskRecord.get('initTime') && !taskRecord.get('projectId')) {
        return true;
    }
}

function updateTasksLength(taskPanel) {
    if (taskPanel.initTimeField.getRawValue()!='' && taskPanel.endTimeField.getRawValue()!='' && taskPanel.endTimeField.isValid() && taskPanel.initTimeField.isValid()) {
        // We write the task's length label
        init = taskPanel.initTimeField.getRawValue().split(':');
        initHour = init[0];
        initMinute = init[1];
        end = taskPanel.endTimeField.getRawValue().split(':');
        endHour = end[0];
        endMinute = end[1];
        if ((endHour == 0) && (endMinute == 0))
            endHour = 24;
        diffHour = endHour - initHour;
        diffMinute = endMinute - initMinute;
        if (diffMinute < 0)
        {
            diffHour--;
            diffMinute += 60;
        }
        if (diffMinute < 10)
            diffMinute = "0" + diffMinute;
        taskPanel.length.setText(diffHour + ":" + diffMinute + " h");
    }
}

var tab = 3;

function updateTitle(p){
    var title = 'Task';

    if (this.initTimeField.getRawValue() != '')
    {
        title += " [ " + this.initTimeField.getRawValue();
    } else title += " [ ?";

    if (this.endTimeField.getRawValue() != '')
    {
        title += " - " + this.endTimeField.getRawValue() + " ]";
    } else title += " - ? ]";

    if (this.descriptionTextArea.getValue() != '')
    {
        var text = this.descriptionTextArea.getValue();
        if (text.length > 115)
            text = text.substr(0,115) + " [...]";
        title += " - " + text;
    }

    this.setTitle(title);
};

function simplifyTitle(p){
    this.setTitle('Task');
}


/**
 * Checks if there are unsaved changes in this page.
 */
function isUnsaved() {
    modifiedRecords = myStore.getModifiedRecords();
    if(modifiedRecords.length == 0) {
        return false;
    }
    if(modifiedRecords.length == 1) {
        if(modifiedRecords[0] == freshCreatedTaskRecord) {
            if(!isUnTouched(freshCreatedTaskRecord)) {
                return true;
            }
        } else {
            return true;
        }
    }
    if(modifiedRecords.length > 1) {
        return true;
    }
    return false;
}

/*  Class that stores a taskRecord element and shows it on screen.
    It keeps the taskRecord in synch with the content of the form on screen,
    in real-time (as soon as it changes). */
var TaskPanel = Ext.extend(Ext.Panel, {
    setReadOnly: function(readOnly) {
        this.initTimeField.setDisabled(readOnly);
        this.endTimeField.setDisabled(readOnly);
        this.customerComboBox.setDisabled(readOnly);
        this.projectComboBox.setDisabled(readOnly);
        this.taskTypeComboBox.setDisabled(readOnly);
        this.storyField.setDisabled(readOnly);
        this.taskStoryComboBox.setDisabled(readOnly);
        this.teleworkCheckBox.setDisabled(readOnly);
        this.onsiteCheckBox.setDisabled(readOnly);
        this.descriptionTextArea.setDisabled(readOnly);

        this.deleteButton.setDisabled(readOnly);
        this.cloneButton.setDisabled(readOnly);
        this.createTemplateButton.setDisabled(readOnly);
    },

    initComponent: function() {

        Ext.apply(this, {
            /* Preconfigured options */
            frame: true,
            title: 'Task',
            monitorResize: true,
            collapsible: true,
            layout:'column',

            /* Inputs of the task form */
            initTimeField: new Ext.form.TimeField({
                parent: this,
                ref: '../initField',
                allowBlank: false,
                width: 60,
                format: 'H:i',
                increment: 15,
                initTimeField: true,
                vtype: 'timerange',
                vtypeText: 'Time must be earlier than the end time.',
                tabIndex: tab++,
                listeners: {
                    'change': function () {
                        this.parent.endTimeField.validate();
                        this.parent.taskRecord.set('initTime',this.getValue());
                        updateTasksLength(this.parent);
                    },
                    'valid': function () {
                        this.parent.taskRecord.set('initTime',this.getRawValue());
                    }
                },
            }),
            length: new Ext.form.Label({
                parent: this,
                ref: '../length',
                style: 'display:block; padding:5px 0 5px 2px'
            }),
            endTimeField: new Ext.form.TimeField({
                parent: this,
                ref: '../endField',
                allowBlank: false,
                width: 60,
                format: 'H:i',
                increment: 15,
                endTimeField: true,
                vtype: 'timerange',
                vtypeText: 'Time must be later than the init time.',
                tabIndex: tab++,
                listeners: {
                    'change': function () {
                        this.parent.initTimeField.validate();
                        this.parent.taskRecord.set('endTime',this.getValue());
                        updateTasksLength(this.parent);
                    },
                    'valid': function () {
                        this.parent.taskRecord.set('endTime',this.getRawValue());
                    }
                },
            }),
            customerComboBox: new Ext.form.ComboBox({
                parent: this,
                tabIndex: tab++,
                disabled: true,
                mode: 'local',
                typeAhead: true,
                triggerAction: 'all',
                forceSelection: true,
            }),
            projectComboBox: new Ext.form.ComboBox({
                parent: this,
                tabIndex: tab++,
                flex:1,
                store: new Ext.data.Store({
                    parent: this,
                    autoLoad: true,  //initial data are loaded in the application init
                    autoSave: false, //if set true, changes will be sent instantly
                    baseParams: {
                        'login': user,
                        'order': 'description',
                        'active': 'true',
                    },
                    filter: function(property, value, anyMatch, caseSensitive) {
                        var fn;
                        if (((property == 'description') || (property == 'customerName')) && !Ext.isEmpty(value, false)) {
                            value = this.data.createValueMatcher(value, anyMatch, caseSensitive);
                            fn = function(r){
                                return value.test(r.data['description']) || value.test(r.data['customerName']);
                            };
                        } else {
                            fn = this.createFilterFn(property, value, anyMatch, caseSensitive);
                        }
                        return fn ? this.filterBy(fn) : this.clearFilter();
                    },
                    proxy: new Ext.data.HttpProxy({url: 'services/getProjectsService.php', method: 'GET'}),
                    reader:new Ext.data.XmlReader({record: 'project', id:'id' }, projectRecord),
                    remoteSort: false,
                    listeners: {
                        'load': function (store) {
                            var dummyRecord = new projectRecord({
                                id: -1,                  // some invalid id
                                description: "Load all projects",
                                customerName: ""
                            });

                            store.add(dummyRecord);
                            store.commitChanges();

                            //the value of projectComboBox has to be set after loading the data on this store
                            if ((this.findExact("id", this.parent.taskRecord.data['projectId']) == -1) &&
                                    (this.parent.taskRecord.data['projectId'] > 0)) {
                                //no project with that id was found in this store
                                if(this.baseParams.login) {
                                    //the project could not be found as the current user is not assigned to it
                                    //lets load the entire list of projects and check
                                    this.parent.projectComboBox.store.setBaseParam('login',null);
                                    this.parent.projectComboBox.store.load();
                                } else if(this.parent.taskRecord.id == null) {
                                    //this is a cloned task
                                    //the original task belonged to a closed project
                                    //we remove the project value
                                    this.parent.projectComboBox.setValue(null);
                                    this.parent.taskRecord.set('projectId', null);
                                } else {
                                    //this is a saved task belonging to a closed project
                                    //we disable edition for this task and reload
                                    //the list with that only project
                                    this.parent.setReadOnly(true);
                                    this.proxy.setUrl('services/getProjectService.php', true);
                                    this.setBaseParam('pid', this.parent.taskRecord.data['projectId']);
                                    this.load();
                                }
                            } else if(this.parent.taskRecord.data['projectId'] != -1) {
                                // If the project has an association with a customer, do show it in the select box
                                projectName = this.getAt(
                                    this.findExact('id', this.parent.taskRecord.data['projectId'])
                                ).data['description'];

                                // Get the correct customer name
                                customerName = this.getAt(
                                    this.findExact('id', this.parent.taskRecord.data['projectId'])
                                ).data['customerName'];

                                selectText = customerName ? projectName + " - " + customerName : projectName;
                                this.parent.projectComboBox.setValue(selectText);
                                this.parent.projectComboBox.value = this.parent.taskRecord.data['projectId'];
                                this.parent.customerComboBox.setValue(customerName);

                                Ext.QuickTips.register({
                                    target: this.parent.projectComboBox,
                                    text: projectName,
                                });
                            }
                        }
                    },
                }),
                mode: 'local',
                valueField: 'id',
                triggerAction: 'all',
                forceSelection: true,
                displayField: 'description',
                tpl: '<tpl for="."><div class="x-combo-list-item" > <tpl>{description} </tpl>' +
                        '<tpl if="customerName">- {customerName}</tpl></div></tpl>',
                listeners: {
                    'select': function (combo, record, index) {
                        if(record.data['id'] == -1) {
                            this.store.setBaseParam('login',null);
                            this.store.load();
                            this.clearValue();
                            return;
                        }

                        customerName = "";
                        selectText = record.data['description'];

                        this.parent.taskRecord.set('projectId', record.id);
                        // We take customer name from the select combo, and injects its id to the taskRecord
                        if (record.data['customerName']) {
                            customerName = record.data['customerName'];
                            selectText = record.data['description'] + " - " + record.data['customerName'];
                        }

                        // Set the custom value for the select combo box
                        this.setValue(selectText);
                        combo.value = record.id;

                        this.parent.taskRecord.set('taskStoryId', "");
                        this.parent.taskStoryComboBox.setValue("");
                        this.parent.customerComboBox.setValue(customerName);

                        Ext.QuickTips.register({
                            target: this.parent.projectComboBox,
                            text: record.data['description'],
                        });
                    },
                    'blur': function () {
                        // workaround in case you set a value, save with ctrl+s,
                        // delete the value and change the focus. In that case,
                        // 'select' or 'change' events wouldn't be triggered.
                        this.parent.taskRecord.set('projectId',this.getValue());
                        this.parent.taskRecord.set('taskStoryId', "");

                        //invoke changes in the "TaskStory" combo box
                        this.parent.taskStoryComboBox.store.setBaseParam('pid',this.parent.taskRecord.data['projectId']);
                        this.parent.taskStoryComboBox.store.load();
                    },
                }
            }),
            storyField: new Ext.form.TextField({
                parent: this,
                value: this.taskRecord.data['story'],
                tabIndex: tab++,
                style: "width: 92.5%",
                enableKeyEvents: true,
                listeners: {
                    'change': function () {
                        this.parent.taskRecord.set('story',Trim(this.getValue()));
                    },
                    'keyup': function () {
                        this.parent.taskRecord.set('story',Trim(this.getValue()));
                    },
                    'blur': function () {
                        this.setValue(Trim(this.getValue()));
                    }
                }
            }),
            descriptionTextArea: new Ext.form.TextArea({
                parent: this,
                height: 110,
                style: "width: 100%",
                columnWidth: 1,
                tabIndex: tab++,
                value: this.taskRecord.data['text'],
                enableKeyEvents: true,
                listeners: {
                    'keyup': function () {
                        this.parent.taskRecord.set('text',Trim(this.getValue()));
                    },
                    'change': function () {
                        this.parent.taskRecord.set('text',Trim(this.getValue()));
                    },
                    'blur': function () {
                        this.setValue(Trim(this.getValue()));
                    }
                }
            }),
            taskTypeComboBox: new Ext.form.ComboBox({
                parent: this,
                value: this.taskRecord.data['ttype'],
                tabIndex: tab++,
                valueField: 'value',
                displayField: 'displayText',
                mode: 'local',
                typeAhead: true,
                triggerAction: 'all',
                forceSelection: true,
                store: new Ext.data.ArrayStore({
                    fields: [
                        'value',
                        'displayText'
                    ],
                    data: [
                        ['administration', 'Administration'],
                        ['analysis', 'Analysis'],
                        ['community', 'Community'],
                        ['coordination', 'Coordination'],
                        ['demonstration', 'Demonstration'],
                        ['deployment', 'Deployment'],
                        ['design', 'Design'],
                        ['documentation', 'Documentation'],
                        ['environment', 'Environment'],
                        ['implementation', 'Implementation'],
                        ['maintenance', 'Maintenance'],
                        ['publication', 'Publication'],
                        ['requirements', 'Requirements'],
                        ['sales', 'Sales'],
                        ['sys_maintenance', 'Systems maintenance'],
                        ['teaching', 'Teaching'],
                        ['technology', 'Technology'],
                        ['test', 'Test'],
                        ['training', 'Training'],
                        ['traveling', 'Traveling'],
                    ],
                }),
                listeners: {
                    'select': function () {
                        this.parent.taskRecord.set('ttype',this.getValue());
                    },
                    // workaround in case you set a value, save with the hotkey,
                    // delete the value and change the focus. In that case,
                    // 'select' or 'change' events wouldn't be triggered.
                    'blur': function () {
                        this.parent.taskRecord.set('ttype',this.getValue());
                    }
                }
            }),
            taskStoryComboBox: new Ext.form.ComboBox({
                parent: this,
                tabIndex: tab++,
                width: 180,
                store: new Ext.data.Store({
                    parent: this,
                    autoLoad: true,  //initial data are loaded in the application init
                    autoSave: false, //if set true, changes will be sent instantly
                    baseParams: {
                        'uidActive': true,
                        'pid': this.taskRecord.data['projectId'],
                    },
                    proxy: new Ext.data.HttpProxy({url: 'services/getOpenTaskStoriesService.php', method: 'GET'}),
                    reader:new Ext.data.XmlReader({record: 'taskStory', id:'id' }, taskStoryRecord),
                    remoteSort: false,
                    listeners: {
                        'load': function () {
                            //the value of projectComboBox has to be set after loading the data on this store
                            this.parent.taskStoryComboBox.setValue(this.parent.taskRecord.data['taskStoryId']);
                        }
                    },
                }),
                mode: 'local',
                valueField: 'id',
                typeAhead: true,
                triggerAction: 'all',
                displayField: 'friendlyName',
                forceSelection: true,
                listeners: {
                    'select': function () {
                        this.parent.taskRecord.set('taskStoryId',this.getValue());
                    },
                    'blur': function () {
                        // workaround in case you set a value, save with ctrl+s,
                        // delete the value and change the focus. In that case,
                        // 'select' or 'change' events wouldn't be triggered.
                        this.parent.taskRecord.set('taskStoryId',this.getValue());
                    },
                }
            }),
            teleworkCheckBox: new Ext.form.Checkbox({
                parent: this,
                value: this.taskRecord.data['telework']=='true',
                boxLabel: "Telework",
                tabIndex: tab++,
                listeners: {
                    'check': function() {
                        this.parent.taskRecord.set('telework',String(this.getValue()));
                    }
                }
            }),
            onsiteCheckBox: new Ext.form.Checkbox({
                parent: this,
                value: this.taskRecord.data['onsite']=='true',
                boxLabel: "Onsite",
                tabIndex: tab++,
                listeners: {
                    'check': function() {
                        this.parent.taskRecord.set('onsite',String(this.getValue()));
                    }
                }
            }),
            deleteButton: new Ext.Button({
                parent: this,
                text:'Delete',
                tabIndex: tab++,
                width: 60,
                margins: "7px 0 0 5px",
                handler: function() {
                    // We remove the TaskRecord from the Store, the TaskPanel
                    // from the parent panel and reload it
                    this.parent.store.remove(this.parent.taskRecord);
                    this.parent.parent.remove(this.parent);
                    this.parent.parent.doLayout();
                }
            }),
            cloneButton: new Ext.Button({
                parent: this,
                text:'Clone',
                tabIndex: tab++,
                width: 60,
                margins: "7px 0 0 5px",
                handler: function() {
                    newTask = this.parent.taskRecord.copy();
                    Ext.data.Record.id(newTask);
                    this.parent.store.add(newTask);
                    taskPanel = new TaskPanel({
                        parent: this.parent.parent,
                        taskRecord:newTask,
                        store: this.parent.store,
                        listeners: {
                            'collapse': updateTitle,
                            'beforeexpand': simplifyTitle,
                        },
                    });
                    this.parent.parent.add(taskPanel);
                    taskPanel.doLayout();
                    this.parent.parent.doLayout();

                    // We set the current time as end and empty as init
                    var now = new Date();
                    taskPanel.endTimeField.setRawValue(now.format('H:i'));
                    newTask.set('endTime',now.format('H:i'));
                    newTask.set('initTime','');
                    taskPanel.endTimeField.validate();
                    taskPanel.initTimeField.setRawValue('');

                    taskPanel.initTimeField.focus();

                    // If contents don't fit the screen, scroll to the new task
                    var content = document.getElementById('content');
                    if( HEADER_HEIGHT + content.scrollHeight > window.innerHeight)
                        window.scrollTo(0, taskPanel.getEl().getY());
                }
            }),
            createTemplateButton: new Ext.Button({
                parent: this,
                text:'Template',
                tabIndex: tab++,
                margins: "7px 0 0 5px",
                width: 60,
                handler: function() {
                    var task = this.parent.taskRecord;

                    Ext.Msg.prompt('Template', 'Please enter template name:', function (btn, text, cfg) {
                        if (btn == 'ok' && Ext.isEmpty(text)) {
                            var newMsg = '<span style="color:red">Please enter template name:</span>';
                            Ext.Msg.show(Ext.apply({}, {msg: newMsg}, cfg));
                        }

                        if(text) {
                            var newTemplate = new templateRecord();
                            newTemplate.set('name',text);
                            newTemplate.set('text',task.get('text'));
                            newTemplate.set('projectId', task.get('projectId'));
                            newTemplate.set('ttype', task.get('ttype'));
                            newTemplate.set('story', task.get('story'));
                            newTemplate.set('taskStoryId', task.get('taskStoryId'));
                            newTemplate.set('telework', task.get('telework'));
                            newTemplate.set('onsite', task.get('onsite'));
                            //add the record to the store, it will trigger a save operation
                            Ext.StoreMgr.get('templatesStore').add(newTemplate);
                        }
                    });
                }
            }),
        });
        /* Set the value of the checkboxes correctly */
        this.teleworkCheckBox.setValue((this.taskRecord.data['telework']=='true'));
        this.onsiteCheckBox.setValue((this.taskRecord.data['onsite']=='true'));

        topBox = new Ext.Panel({
            layout: 'column',
            columnWidth: 1,
            style:"padding-bottom:3px",
            items: [
                new Ext.Container({
                    layout: 'hbox',
                    width:170,
                    items:[
                        this.initTimeField,
                        new Ext.form.Label({text: ' - ', style:'display:block; padding:5px 2px 5px 2px'}),
                        this.endTimeField,
                        this.length,
                    ]
                }),
                new Ext.Container({
                    columnWidth: 1,
                    layout: 'hbox',
                    items:[
                        new Ext.form.Label({text: 'Project', style:'display:block; padding:5px 2px 5px 0px'}),
                        this.projectComboBox,
                        new Ext.Container({
                            layout: 'hbox',
                            flex:1,
                            items:[
                                new Ext.form.Label({text: 'Story', style: 'display:block; padding:5px 2px 5px 2px'}),
                                this.storyField,
                            ]
                        }),
                    ]
                }),

            ]
        });
        centerBox = new Ext.Panel({
            layout: 'column',
            columnWidth: 1,
            monitorResize: true,
            items:[
                this.descriptionTextArea,
                new Ext.Container({
                    width:60,
                    layout: 'anchor',
                    items: [
                        this.deleteButton,
                        this.cloneButton,
                        this.createTemplateButton,
                    ]
                }),
            ]
        });
        bottomBox = new Ext.Panel({
            layout: 'hbox',
            layoutConfig: {defaultMargins: "5px 0px 0px 2px"},
            columnWidth: 1,
            items:[
                new Ext.Container({
                    layout: 'column',
                    style:"padding-right:3px",
                    items: [
                        new Ext.form.Label({text: 'Task type', style: 'display:block; padding:5px 2px 5px 2px'}),
                        this.taskTypeComboBox,
                        new Ext.form.Label({text: 'TaskStory', style: 'display:block; padding:5px 2px 5px 2px'}),
                        this.taskStoryComboBox,
                    ]
                }),
                new Ext.Container({
                    layout: 'column',
                    layoutConfig: {defaultMargins: "7px 5px 10px 10px"},
                    items: [this.teleworkCheckBox, this.onsiteCheckBox ]
                }),
            ]
        });
        this.items = [topBox, centerBox, bottomBox];

        /* call the superclass to preserve base class functionality */
        TaskPanel.superclass.initComponent.apply(this, arguments);
    }
});


Ext.onReady(function(){

    Ext.QuickTips.init();

    /* Container for the TaskPanels (with scroll bars enabled) */
    var tasksScrollArea = new Ext.Container({autoScroll:true,  renderTo: 'tasks'});

    /* Proxy to the services related with load/save tasks */
    var myProxy = new Ext.data.HttpProxy({
    method: 'POST',
        api: {
            read    : {url: 'services/getUserTasksService.php', method: 'GET'},
            create    : 'services/createTasksService.php',
            update  : 'services/updateTasksService.php',
            destroy : 'services/deleteTasksService.php'
        },
    });

    /* Store to load/save tasks */
    myStore = new Ext.ux.TasksStore({
        autoLoad: true,  //initial data are loaded in the application init
        autoSave: false, //if set true, changes will be sent instantly
        baseParams: {
            'login': user,
            'date': date,
            'dateFormat': 'Y-m-d',
        },
        storeId: 'id',
        proxy: myProxy,
        reader:new Ext.data.XmlReader({record: 'task', successProperty: 'success', idProperty:'id' }, taskRecord),
        writer:new Ext.data.XmlWriter({
            xmlEncoding: 'UTF-8',
            writeAllFields: false,
            tpl: '<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
                '<tpl if="records.length &gt; 0">' +
                    '<tpl if="root">' +
                        '<tasks>' +
                            '<tpl for="records">' +
                                '<task>' +
                                    '<date>' + date  + '</date>' +
                                    '<tpl for=".">' +
                                        '<{name}>{value}</{name}>' +
                                    '</tpl>' +
                                '</task>' +
                            '</tpl>' +
                        '</tasks>' +
                    '</tpl>' +
                '</tpl>'
                }, taskRecord),
        remoteSort: false,
        sortInfo: {
            field: 'initTime',
            direction: 'ASC',
        },
        listeners: {
            'load': function (store, records, options) {
                if (options.add == true) {
                    for (i in records) {
                        if (i >= 0) {
                            records[i].markDirty();
                            records[i].id = null;
                            records[i].phantom = true;
                            records[i].data['date'] = date;
                            store.remove(records[i]);
                            store.add(records[i]);
                            var r = records[i];
                            taskPanel = new TaskPanel({
                                parent: tasksScrollArea,
                                store: myStore,
                                taskRecord: r,
                                listeners: {
                                    'collapse': updateTitle,
                                    'beforeexpand': simplifyTitle,
                                },
                            });
                            tasksScrollArea.add(taskPanel);
                            taskPanel.doLayout();
                            tasksScrollArea.doLayout();

                            // We set the time values as raw ones, just for avoiding
                            // infinite validations
                            taskPanel.initTimeField.setRawValue(r.data['initTime']);
                            taskPanel.initTimeField.validate();
                            taskPanel.endTimeField.setRawValue(r.data['endTime']);
                            taskPanel.endTimeField.validate();

                            if (forbidden) {
                                taskPanel.setReadOnly(true);
                            }

                            updateTasksLength(taskPanel);
                        }
                    }
                } else {
                    this.each(function(r) {
                        taskPanel = new TaskPanel({
                            parent: tasksScrollArea,
                            store: myStore,
                            taskRecord:r,
                            listeners: {
                                'collapse': updateTitle,
                                'beforeexpand': simplifyTitle,
                            },
                        });
                        tasksScrollArea.add(taskPanel);
                        taskPanel.doLayout();
                        tasksScrollArea.doLayout();
                        if(cookieProvider.get('tasksCollapsed')) {
                            taskPanel.collapse();
                        }

                        // We set the time values as raw ones, just for avoiding
                        // infinite validations
                        taskPanel.initTimeField.setRawValue(r.data['initTime']);
                        taskPanel.initTimeField.validate();
                        taskPanel.endTimeField.setRawValue(r.data['endTime']);
                        taskPanel.endTimeField.validate();

                        if(forbidden) {
                            taskPanel.setReadOnly(true);
                        }

                        updateTasksLength(taskPanel);
                    });
                    // Mark every item has loaded
                    loaded = true;
                }
            },
            'save': function () {
                if (!myStore.error) {
                    Ext.getCmp('status_display').setText("Status: saved at "+ new Date());
                    if(!myStore.autoSaved) {
                        App.setAlert(true, "Task Records Changes Saved");
                    }
                    summaryStore.load();
                }
                myStore.error = false;
            },
            'exception': function () {
                App.setAlert(false, "Some Error Occurred While Saving The Changes (please check you haven't clipped working hours)");
                myStore.error = true;
            },
            'update': function () {
                Ext.getCmp('status_display').setText("Status: Pending changes");
            },
            'remove': function () {
                Ext.getCmp('status_display').setText("Status: Pending changes requiring manual save");
            }
        }
    });

    /* Proxy to load the personal summary */
    var summaryProxy = new Ext.data.HttpProxy({
    method: 'GET',
        api: {
            read    : 'services/getPersonalSummaryByDateService.php',
        },
    });
    /* Store to load the personal summary */
    var summaryStore = new Ext.data.Store({
        baseParams: {
            'date': date,
            'dateFormat': 'Y-m-d',
        },
        storeId: 'summaryStore',
        proxy: summaryProxy,
        reader:new Ext.data.XmlReader({record: 'hours', idProperty:'month' }, summaryRecord),
        remoteSort: false,
        listeners: {
            'load': function () {
                Ext.getCmp('month').setValue(summaryStore.getAt(0).get('month') + " h");
                Ext.getCmp('day').setValue(summaryStore.getAt(0).get('day') + " h");
                Ext.getCmp('week').setValue(summaryStore.getAt(0).get('week') + " h");
                Ext.getCmp('weekly_goal').setValue(summaryStore.getAt(0).get('weekly_goal') + " h");
            },
        }
    });

    /* Add a callback to add new tasks */
    function newTask(freshCreatedTask) {
        /*We have to wait till the entire list gets loaded, otherwise the new
        item do not get saved.*/
        if (isLoaded()) {
            newTask = new taskRecord();
            myStore.add(newTask);
        } else {
            window.setTimeout(newTask, 100);
        }

        taskPanel = new TaskPanel({
            parent: tasksScrollArea,
            taskRecord:newTask,
            store: myStore,
            listeners: {
                'collapse': updateTitle,
                'beforeexpand': simplifyTitle,
            },
        });

        if(typeof(freshCreatedTask) === 'boolean' && freshCreatedTask == true) {
            freshCreatedTaskRecord = newTask;
            freshCreatedTaskPanel = taskPanel;
        }

        tasksScrollArea.add(taskPanel);
        taskPanel.doLayout();
        tasksScrollArea.doLayout();

        // We set the current time as end
        var now = new Date();
        taskPanel.endTimeField.setRawValue(now.format('H:i'));
        newTask.set('endTime',now.format('H:i'));
        taskPanel.endTimeField.validate();

        // Put the focus on the init time field
        taskPanel.initTimeField.focus();

        // If contents don't fit the screen, scroll to the new task
        var content = document.getElementById('content');
        if( HEADER_HEIGHT + content.scrollHeight > window.innerHeight)
            window.scrollTo(0, taskPanel.getEl().getY());
        // For a fresh task, we need to keep the status as draft, as we havent saved it yet!
        Ext.getCmp('status_display').setText("Status: Draft");
    }

    // Validate the inputs in the task panel
    function validateTasks() {
        var panels = tasksScrollArea.items;
        for(var panel=0; panel<panels.getCount(); panel++) {
            if (!panels.get(panel).initTimeField.isValid() || !panels.get(panel).endTimeField.isValid()) {
                return false;
            }
        }
        return true;
    }

    // Add the new task to the taskstore
    function addToMyStore() {
        myStore.each(function(r) {
            if (r.data['story'] != undefined)
                r.data['story'] = xmlencode(r.data['story']);
            if (r.data['text'] != undefined)
                r.data['text'] = xmlencode(r.data['text']);
        });
        if (myStore.save()) {
            return true;
        }
        return false;
    }

    /* Add a callback to save tasks */
    function saveTasks() {
        // First we check if the time fields of all records are valid, and then save
        if (validateTasks()) {
            myStore.autoSaved = false;
            addToMyStore();
        } else  // Otherwise, we print the error message
          App.setAlert(false, "Check For Invalid Field Values");
    }

    // Implement autosave of data, when valid contents are typed in to the task fields
    window.setInterval(function () {
        if(isUnsaved()) {
            if(validateTasks()) {
                myStore.autoSaved = true;
                addToMyStore();
            }
        }
    }, 10000);

    /* Build a calendar on the auxiliar sidebar */
    new Ext.Panel({
        renderTo: Ext.get("calendarpanel"),
        items: [
            new Ext.ux.DatePickerPlus({
                allowMouseWheel: false,
                showWeekNumber: true,
                multiselection: false,
                customLinkUrl: 'tasks.php?date=',
                selectedDates: [Date.parseDate(date, 'Y-m-d')],
                value: Date.parseDate(date, 'Y-m-d'),
                startDay: 1,
                listeners: {'select': function (item, date) {
                window.location = "tasks.php?date=" + date.format('Y-m-d');
        }}
            }),
        ],
    });

    // Cloning Panel
    var cloningPanel = new Ext.FormPanel({
        width: 204,
        height: 65,
        renderTo: Ext.get('calendarpanel'),
        frame:true,
        layout: {
            type: 'vbox',
            align: 'center',
        },
        header: false,
        items: [
            {
                name: 'cloneDate',
                id: 'cloneDate',
                hideLabel: true,
                width: 160,
                xtype: 'datefieldplus',
                format: 'd/m/Y',
                value: lastTaskDate,
                allowBlank: false,
                startDay: 1,
            }, new Ext.Button({
                text:'Copy tasks from selected date',
                width: 60,
                margins: "7px 0 0 0px",
                handler: function() {
                    if (Ext.getCmp('cloneDate').isValid())
                    {
                        var cloneDate = Ext.getCmp('cloneDate').getValue();
                        var dateString = cloneDate.getFullYear() + '-';
                        if (cloneDate.getMonth() <= 8)
                            dateString += '0';
                        dateString += (cloneDate.getMonth()+1) + '-';
                        if (cloneDate.getDate() <= 9)
                            dateString += '0';
                        dateString += cloneDate.getDate();

                        // If a fresh empty task exist, just remove it
                        if(freshCreatedTaskRecord && freshCreatedTaskRecord.dirty && isUnTouched(freshCreatedTaskRecord)) {
                            myStore.remove(freshCreatedTaskRecord);
                            tasksScrollArea.remove(freshCreatedTaskPanel);
                            freshCreatedTaskPanel.doLayout();
                            tasksScrollArea.doLayout();
                        }

                        // We load that day's tasks and append them into tasks' store
                        myStore.load({
                            params: {'date': dateString},
                            add: true,
                        });
                    }
                }
            }),
    ]});


    // Summary Panel
    var summaryPanel = new Ext.FormPanel({
        width: 204,
        labelWidth: 70,
        renderTo: Ext.get('summarypanel'),
        frame:true,
        title: 'User Work Summary',
        bodyStyle: 'padding:5px 5px 0px 5px;',
        defaults: {
            width: 100,
            labelStyle: 'text-align: right; width: 70; font-weight:bold; padding: 0 0 0 0;',
        },
        defaultType:'displayfield',
        items: [{
            id:'day',
            name: 'day',
            fieldLabel:'Today',
        },{
            id:'week',
            name: 'week',
            fieldLabel:'This week',
        },{
            id:'month',
            name: 'month',
            fieldLabel:'This month',
        },{
            id:'weekly_goal',
            name: 'weekly_goal',
            fieldLabel:'Week goal',
        }

        ]
    });

    // Expand/collapse all Panel
    var expandCollapseAllPanel = new Ext.Panel({
        width: 204,
        defaults: {
            width: '100%',
        },
        items: [
            new Ext.Button({
                text:'Expand all',
                handler: function() {
                    var panels = tasksScrollArea.items;
                    for(var i=0; i<panels.getCount(); i++) {
                        panels.get(i).expand();
                    }
                    cookieProvider.set('tasksCollapsed', false);
                }
            }),
            new Ext.Button({
                text:'Collapse all',
                handler: function() {
                    var panels = tasksScrollArea.items;
                    for(var i=0; i<panels.getCount(); i++) {
                        panels.get(i).collapse();
                    }
                    cookieProvider.set('tasksCollapsed', true);
                }
            })
        ]
    });

    // Templates Panel
    var templatesPanel = new Ext.Panel({
        id: 'templatesPanel',
        width: 204,
        defaults: {
            width: '100%',
        },
        addButtonForTemplate: function (templateValues) {
            var createButton = new Ext.Button({
                text: ((templateValues['name'] != undefined) &&
                        (templateValues['name'] != '')) ?
                    templateValues['name'] :
                    'Template',
                flex: 3,
                disabled: forbidden,
                handler: function () {
                    /*We have to wait till the entire list gets loaded, otherwise the new
                     item do not get saved.*/
                    if (isLoaded()) {
                        //create and populate a record
                        var newTask = new taskRecord();
                    } else {
                        window.setTimeout(createButton.handler, 100);
                    }

                    // If a fresh empty task exist, just remove it
                    if(freshCreatedTaskRecord && freshCreatedTaskRecord.dirty && isUnTouched(freshCreatedTaskRecord)) {
                        myStore.remove(freshCreatedTaskRecord);
                        tasksScrollArea.remove(freshCreatedTaskPanel);
                        freshCreatedTaskPanel.doLayout();
                        tasksScrollArea.doLayout();
                    }
                    // When you create a new task, lets keep the status as draft, as its not saved yet.
                    Ext.getCmp('status_display').setText("Status: Draft");

                    newTask.set('projectId', templateValues['projectId']);
                    newTask.set('ttype', templateValues['ttype']);
                    newTask.set('story', templateValues['story']);
                    newTask.set('taskStoryId', templateValues['taskStoryId']);
                    newTask.set('text', templateValues['text']);
                    // For a fresh template, the templateValue of bool fields return '1'
                    if( templateValues['telework'] == '1' || templateValues['telework'] == 'true' ) {
                        newTask.set('telework', 'true');
                    }
                    if( templateValues['onsite'] == '1' || templateValues['onsite'] == 'true' ) {
                        newTask.set('onsite', 'true');
                    }
                    //add the record to the store
                    myStore.add(newTask);

                    //create and show a panel for the task
                    var taskPanel = new TaskPanel({
                        parent: tasksScrollArea,
                        taskRecord:newTask,
                        store: myStore,
                        listeners: {
                            'collapse': updateTitle,
                            'beforeexpand': simplifyTitle,
                        },
                    });
                    tasksScrollArea.add(taskPanel);
                    taskPanel.doLayout();
                    tasksScrollArea.doLayout();

                    //put the focus on the init time field
                    taskPanel.initTimeField.focus();

                    // If contents don't fit the screen, scroll to the new task
                    var content = document.getElementById('content');
                    if( HEADER_HEIGHT + content.scrollHeight > window.innerHeight)
                        window.scrollTo(0, taskPanel.getEl().getY());
                },
            });
            var deleteButton = new Ext.Button({
                text: 'Delete',
                flex: 1,
                handler: function () {
                    //remove from the store
                    store = Ext.StoreMgr.get('templatesStore')
                    store.remove(store.getById(templateValues['id']));

                    //remove from the panel
                    var row = this.findParentByType('panel');
                    var panel = row.findParentByType('panel');
                    panel.remove(row);
                },
            });
            this.add(new Ext.Panel({
                layout: 'hbox',
                items: [createButton, deleteButton],
            }));
            this.doLayout();
        },
    });

    // Populate templates panel
    var templatesStore = new Ext.data.Store({
        autoLoad: true,
        autoSave: true,
        storeId: 'templatesStore',
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
            api: {
                read    : {url: 'services/getUserTemplatesService.php', method: 'GET'},
                destroy : 'services/deleteTemplatesService.php',
                create  : 'services/createTemplatesService.php'
            },
        }),
        listeners: {
            'load': function (store, records, options) {
                store.each(function(r) {
                    templatesPanel.addButtonForTemplate(r.data);
                });
            },
            'save': function (store, batch, data) {
                if(data.create !== undefined) {
                    data.create.forEach(function(r) {
                        templatesPanel.addButtonForTemplate(r);
                    });
                }
            }
        }
    });

    // Actions panels
    var expandCollapseAllPanel = new Ext.Panel({
        width: 204,
        renderTo: Ext.get('actionspanel'),
        frame:true,
        title: 'Actions',
        defaults: {
            width: '100%',
        },
        items: [
            new Ext.form.Label({
                text: 'Tasks'
            }),
            new Ext.Button({
                text:'New',
                handler: newTask,
                disabled: forbidden,
            }),
            new Ext.Button({
                text:'Save',
                handler: saveTasks,
                disabled: forbidden,
            }),
            new Ext.menu.Separator(),
            new Ext.form.Label({
                text: 'Panels'
            }),
            expandCollapseAllPanel,
            new Ext.menu.Separator(),
            new Ext.form.Label({
                text: 'Templates'
            }),
            templatesPanel
        ],
    });
    var expandCollapseAllPanel = new Ext.Toolbar({
        renderTo: Ext.get('moreactions'),
        items: [
            new Ext.Button({
                text:'New task',
                handler: newTask,
                disabled: forbidden,
            }),
            '-',
            new Ext.Button({
                text:'Save changes',
                handler: saveTasks,
                disabled: forbidden,
            }), '-',
            {
                xtype: 'tbtext',
                id: 'status_display',
                text: 'Status: No changes detected'
            }
        ],
    });
    //hotkeys
    new Ext.KeyMap(document, {
        key: 's',
        ctrl: true,
        stopEvent: true,
        handler: saveTasks,
    });
    new Ext.KeyMap(document, {
        //alternate shortcut for Epiphany
        key: 's',
        alt: true,
        stopEvent: true,
        handler: saveTasks,
    });
    new Ext.KeyMap(document, {
        key: 'un',
        ctrl: true,
        stopEvent: true,
        handler: newTask
    });
    new Ext.KeyMap(document, {
        //alternate shortcut for Epiphany
        key: 'un',
        alt: true,
        stopEvent: true,
        handler: newTask
    });
    new Ext.KeyMap(document, {
        key: '123456789',
        ctrl: true,
        stopEvent: true,
        handler: function (key, event) {
            var i = key - 49; //49 is the key code for '1'

            tasksScrollArea.items.get(i).initTimeField.focus();
            //TODO: fix problem with blur event, see: http://stackoverflow.com/questions/8656165/combo-doesnt-blur-when-manually-shifting-focus
        }
    });

    summaryStore.load();

    // Wait for the page to load, and check if the day is empty to add in a new
    // empty task
    function addEmptyTask() {
        if (isLoaded()) {
            if(tasksScrollArea.items.getCount() == 0 && !forbidden) {
                newTask(true);
                Ext.getCmp('status_display').setText("Status: New empty task created for editing");
            }
        } else {
            window.setTimeout(addEmptyTask, 1000);
        }
    }

    // Adds in a new empty task when an empty day is clicked
    addEmptyTask();

});
