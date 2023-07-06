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
    if (min == null || min == "") {
        min = '00:00';
    }
    if (max == null || max == "") {
        max = '23:59';
    }
    min = field.parseDate(min);
    max = field.parseDate(max);
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
]);

/* Schema of the information about projects */
var projectRecord = new Ext.data.Record.create([
    {name:'id'},
    {name:'description'},
    {name:'fullDescription'},
    {name:'customerName'}
]);
/* Schema of the information of the personal summary */
var summaryRecord = new Ext.data.Record.create([
    {name:'day'},
    {name:'week'},
    {name:'weekly_goal'},
    {name:'extra_hours'},
    {name:'pending_holidays'},
    {name:'scheduled_holidays'},
    {name:'available_holidays'},
    {name:'used_holidays'},
    {name:'acc_extra_hours'},

]);
/* Schema of the information about task templates */
var templateRecord = new Ext.data.Record.create([
    {name:'id'},
    {name:'projectId'},
    {name:'ttype'},
    {name:'story'},
    {name:'telework'},
    {name:'onsite'},
    {name:'text'},
    {name:'name'},
    {name:'initTime'},
    {name:'endTime'}
]);

/* Available values and display names for the `taskType` field in tasks */
var taskTypeStore = new Ext.data.JsonStore({
    fields: [
        'value',
        'displayText'
    ],
    root: 'records',
    idProperty: 'value',
    successProperty: 'success',
    url: 'services/getTaskTypes.php',
    autoLoad: 'true',
});

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
    if(!taskRecord.get('initTime') && !taskRecord.get('endTime') && !taskRecord.get('projectId') &&
            !taskRecord.get('story') && !taskRecord.get('text')) {
        return true;
    }
}

// Load the tasks page for a certain date.
// Makes sure no GET parameters are added when we load "today" tasks.
function navigateToDate(newDate) {
    var url = "tasks.php";
    var today = new Date();
    today.setHours(0,0,0,0);
    newDate.setHours(0,0,0,0);
    // we compare getTime() to be able to load the same day on a different
    // month or year.
    if (today.getTime() !== newDate.getTime()) {
        url += "?date=" + newDate.format('Y-m-d');
    }
    window.location = url;
}

function navigateToNextDay() {
    nextDate = new Date();
    nextDate.setTime(currentDate.getTime() + 86400000);
    navigateToDate(nextDate);
}

function navigateToPrevDay() {
    previousDate = new Date();
    previousDate.setTime(currentDate.getTime() - 86400000);
    navigateToDate(previousDate);
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
        if ((endHour == 0) && (endMinute == 0) && (JSON.stringify(init) != JSON.stringify(end)))
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
    modifiedRecords = tasksStore.getModifiedRecords();
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

/**
 * Removes the empty task that is created by default, but only if it is unchanged.
 */
function removeFreshEmptyTask() {
    if(freshCreatedTaskRecord && freshCreatedTaskRecord.dirty && isUnTouched(freshCreatedTaskRecord)) {
        tasksStore.remove(freshCreatedTaskRecord);
        tasksScrollArea.remove(freshCreatedTaskPanel);
        freshCreatedTaskPanel.doLayout();
        tasksScrollArea.doLayout();
    }
}

function getMinutes(time){
    const hours = Number(time.split(':')[0]);
    const minutes = Number(time.split(':')[1]);
    return (hours * 60) + minutes;
}

function checkTaskForOverlap(store){
    let existingTasks = store.data.items.filter(x => x.id && !x.id.startsWith("ext"));
    let unsavedTasks = store.data.items.filter(x => x.id && x.id.startsWith("ext"));

    let overlapping = [];
    let overlapsTasks = false;
    let message = "";

    existingTasks.forEach(x => {
        unsavedTasks.forEach(t => {
            let overlapsTasksDuration = (getMinutes(t.data.endTime) > getMinutes(x.data.initTime) && getMinutes(t.data.initTime) < getMinutes(x.data.endTime));
            let coincidesInitOrEndTimes =  (getMinutes(t.data.initTime) == getMinutes(x.data.initTime) || getMinutes(t.data.endTime) == getMinutes(x.data.endTime));
            if (overlapsTasksDuration || coincidesInitOrEndTimes) {
                overlapsTasks = true;
                overlapping.push(x);
                message += "Task from " + t.data.initTime + " to " + t.data.endTime + " overlaps with task from " + x.data.initTime + " to " + x.data.endTime + ". ";
            }
        })
    });

    return { overlapsOneOrMoreExistingTasks: overlapsTasks, tasksOverlappedWith: overlapping, message: message }
}

/**
 * Creates and show a panel for a task, and adds that task to the store.
 * @param newRecord the task record to be added.
 * @return the TaskPanel object that has been created.
 */
function addTask(newRecord) {
    // Add task to store
    tasksStore.add(newRecord);

    // Create and show a panel for the task
    var taskPanel = new TaskPanel({
        parent: tasksScrollArea,
        taskRecord: newRecord,
        store: tasksStore,
        listeners: {
            'collapse': updateTitle,
            'beforeexpand': simplifyTitle,
        },
    });
    tasksScrollArea.add(taskPanel);
    taskPanel.doLayout();
    tasksScrollArea.doLayout();

    // If contents don't fit the screen, scroll to the new task
    var content = document.getElementById('content');
    if( HEADER_HEIGHT + content.scrollHeight > window.innerHeight)
        window.scrollTo(0, taskPanel.getEl().getY());

    // Change status message to 'draft' due to new, still unsaved task
    Ext.getCmp('status_display').setText("Status: Draft");

    return taskPanel;
}

/**
 * Callback to add a new, empty tasks. Invoked from the "new task" buttons in
 * the UI, from keyboard shortcuts and also called from addNewTaskIfEmpty().
 */
function newTask(freshCreatedTask) {
    // Delay task creation until store gets loaded or it won't be properly added
    if (!isLoaded()) {
        window.setTimeout(newTask, 100);
        return;
    }
    var newTask = new taskRecord();

    // Add record to store and show in a panel
    taskPanel = addTask(newTask);

    if(typeof(freshCreatedTask) === 'boolean' && freshCreatedTask == true) {
        freshCreatedTaskRecord = newTask;
        freshCreatedTaskPanel = taskPanel;
    }

    // Put the focus on the init time field
    taskPanel.initTimeField.focus();
}

/**
 * Check if the day is empty, then add a new, empty task.
 */
function addNewTaskIfEmpty() {
    if(tasksScrollArea.items.getCount() == 0 && !forbidden) {
        newTask(true);
        Ext.getCmp('status_display').setText("Status: New empty task created for editing");
    }
}

/*  Class that stores a taskRecord element and shows it on screen.
    It keeps the taskRecord in synch with the content of the form on screen,
    in real-time (as soon as it changes). */
var TaskPanel = Ext.extend(Ext.Panel, {
    setReadOnly: function(readOnly) {
        this.initTimeField.setDisabled(readOnly);
        this.endTimeField.setDisabled(readOnly);
        this.projectComboBox.setDisabled(readOnly);
        this.taskTypeComboBox.setDisabled(readOnly);
        this.storyField.setDisabled(readOnly);
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
            cls: 'task-panel',

            /* Inputs of the task form */
            initTimeField: new Ext.form.TimeField({
                parent: this,
                value: this.taskRecord.data['initTime'],
                ref: '../initField',
                allowBlank: false,
                width: 60,
                format: 'H:i',
                increment: 15,
                initTimeField: true,
                vtype: 'timerange',
                vtypeText: 'Time must be earlier than the end time.',
                listeners: {
                    'change': function () {
                        this.parent.endTimeField.validate();
                        this.parent.taskRecord.set('initTime',this.getValue());
                        updateTasksLength(this.parent);
                        updateTimes(this.parent.endTimeField, this.getValue(), null, true);
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
                value: this.taskRecord.data['endTime'],
                ref: '../endField',
                allowBlank: false,
                width: 60,
                format: 'H:i',
                increment: 15,
                endTimeField: true,
                vtype: 'timerange',
                vtypeText: 'Time must be later than the init time.',
                listeners: {
                    'change': function () {
                        this.parent.initTimeField.validate();
                        this.parent.taskRecord.set('endTime',this.getValue());
                        updateTasksLength(this.parent);
                        updateTimes(this.parent.initTimeField, null, this.getValue(), false);
                    },
                    'valid': function () {
                        this.parent.taskRecord.set('endTime',this.getRawValue());
                    }
                },
            }),
            projectComboBox: new Ext.form.ComboBox({
                parent: this,
                allowBlank: false,
                flex: 2,
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
                        if (((property == 'fullDescription') || (property == 'customerName')) && !Ext.isEmpty(value, false)) {
                            value = this.data.createValueMatcher(value, anyMatch, caseSensitive);
                            fn = function(r){
                                return value.test(r.data['fullDescription']) || value.test(r.data['customerName']);
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
                                fullDescription: "Load all projects",
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
                                var project = this.getAt(this.findExact('id', this.parent.taskRecord.data['projectId']));

                                this.parent.projectComboBox.setValueFromRecord(project);
                            }
                        }
                    },
                }),
                mode: 'local',
                valueField: 'id',
                triggerAction: 'all',
                forceSelection: true,
                displayField: 'fullDescription',
                // overwrite standard implementation of typeAhead because it
                // conflicts with the filter by customer name
                typeAhead: true,
                onTypeAhead : function () {
                    if(this.store.getCount() > 0) {
                        //get the highlighted project
                        var record = this.store.getAt(this.selectedIndex);

                        // set value of the combo underneath, not altering the text in the box
                        this.value = record.id;
                        this.parent.taskRecord.set('projectId', record.id);
                    }
                },
                setValueFromRecord: function (record) {
                    // Set value and tooltip for the combo box from a project record

                    if (record !== undefined) {
                        // Set the value for the select combo box
                        this.setValue(record.id);

                        Ext.QuickTips.register({
                            target: this.parent.projectComboBox,
                            text: record.data['fullDescription'],
                        });
                    }
                },
                listeners: {
                    'select': function (combo, record, index) {
                        if(record.data['id'] == -1) {
                            this.store.setBaseParam('login',null);
                            this.store.load();
                            this.clearValue();
                            return;
                        }

                        this.setValueFromRecord(record);
                    },
                    'blur': function () {
                        // works in combination with typeAhead, fills the
                        // combo box with the highlighted project
                        var record = this.store.getAt(this.selectedIndex);
                        this.setValueFromRecord(record);

                        // workaround in case you set a value, save with ctrl+s,
                        // delete the value and change the focus. In that case,
                        // 'select' or 'change' events wouldn't be triggered.
                        this.parent.taskRecord.set('projectId',this.getValue());
                    },
                    'collapse': function () {
                        // clear the project by deleting the text and closing
                        // the combo (esc key or tab)
                        var text = this.getRawValue();
                        if (text == "") {
                            this.selectedIndex = -1;
                            this.setValue();
                            this.parent.taskRecord.set('projectId',"");
                            Ext.QuickTips.unregister(this.parent.projectComboBox);
                        }
                    }
                }
            }),
            storyField: new Ext.form.TextField({
                parent: this,
                value: this.taskRecord.data['story'],
                flex: 1,
                enableKeyEvents: true,
                listeners: {
                    'change': function () {
                        this.parent.taskRecord.set('story',this.getValue().trim());
                    },
                    'blur': function () {
                        this.setValue(this.getValue().trim());
                    }
                }
            }),
            descriptionTextArea: new Ext.form.TextArea({
                parent: this,
                height: 110,
                columnWidth: 1,
                autoScroll: true,
                style: "float:none", // workaround for webkit bug #132188
                value: this.taskRecord.data['text'],
                enableKeyEvents: true,
                listeners: {
                    'change': function () {
                        this.parent.taskRecord.set('text',this.getValue().trim());
                    },
                    'blur': function () {
                        this.setValue(this.getValue().trim());
                    }
                }
            }),
            taskTypeComboBox: new Ext.form.ComboBox({
                parent: this,
                width: 170,
                value: this.taskRecord.data['ttype'],
                valueField: 'value',
                displayField: 'displayText',
                mode: 'local',
                typeAhead: true,
                triggerAction: 'all',
                forceSelection: true,
                store: taskTypeStore,
                listeners: {
                    'expand': function() {
                        this.getStore().filterBy(function(record) {
                            return record.json.active;
                        });
                    },
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
            teleworkCheckBox: new Ext.form.Checkbox({
                parent: this,
                value: this.taskRecord.data['telework']=='true',
                boxLabel: "Telework",
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
                listeners: {
                    'check': function() {
                        this.parent.taskRecord.set('onsite',String(this.getValue()));
                    }
                }
            }),
            deleteButton: new Ext.Button({
                parent: this,
                text:'Delete',
                width: 60,
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
                width: 60,
                handler: function() {
                    var newTask = this.parent.taskRecord.copy();
                    Ext.data.Record.id(newTask);

                    // We empty the init and end time
                    newTask.set('initTime','');
                    newTask.set('endTime','');

                    // Add record to store and show in a panel
                    var taskPanel = addTask(newTask);

                    // Put the focus on the init time field
                    taskPanel.initTimeField.focus();
                }
            }),
            createTemplateButton: new Ext.Button({
                parent: this,
                text:'Template',
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
                            newTemplate.set('telework', task.get('telework'));
                            newTemplate.set('onsite', task.get('onsite'));
                            newTemplate.set('initTime', task.get('initTime'));
                            newTemplate.set('endTime', task.get('endTime'));
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

        bottomBoxTaskItems = [
                        new Ext.form.Label({text: 'Task type', style: 'display:block; padding:5px 2px 5px 2px'}),
                        this.taskTypeComboBox
        ]

        bottomBoxItems = [
            new Ext.Container({
                width: 186,
                layout: 'hbox',
                items:[
                    this.deleteButton,
                    this.cloneButton,
                    this.createTemplateButton,
                ]
            }),
            new Ext.Container({
                width: 145,
                layout: 'hbox',
                items:[
                    this.teleworkCheckBox,
                    this.onsiteCheckBox
                ]
            }),
            new Ext.Container({
                width: 460,
                layout: 'hbox',
                items: bottomBoxTaskItems
            })
        ]

        topBox = new Ext.Panel({
            layout: 'column',
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
                        new Ext.form.Label({text: 'Story', style: 'display:block; padding:5px 2px 5px 2px'}),
                        this.storyField,
                    ]
                }),

            ]
        });
        centerBox = new Ext.Panel({
            layout: 'column',
            monitorResize: true,
            items:[
                this.descriptionTextArea,
            ]
        });
        bottomBox = new Ext.Panel({
            layout: 'column',
            defaults: {
                layoutConfig: {defaultMargins: "5px 2px 0px 0px"}
            },
            items: bottomBoxItems
        });
        this.items = [topBox, centerBox, bottomBox];

        /* call the superclass to preserve base class functionality */
        TaskPanel.superclass.initComponent.apply(this, arguments);
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
        'date': dateString,
        'dateFormat': 'Y-m-d',
    },
    storeId: 'summaryStore',
    proxy: summaryProxy,
    reader:new Ext.data.XmlReader({record: 'hours', idProperty:'month' }, summaryRecord),
    remoteSort: false,
    listeners: {
        'load': function () {
            Ext.getCmp('day').setValue(summaryStore.getAt(0).get('day') + " h");
            Ext.getCmp('week').setValue(summaryStore.getAt(0).get('week') + " h");
            Ext.getCmp('weekly_goal').setValue(summaryStore.getAt(0).get('weekly_goal') + " h");
            Ext.getCmp('extra_hours').setValue(summaryStore.getAt(0).get('extra_hours') + " h");
            Ext.getCmp('available_holidays').setValue(summaryStore.getAt(0).get('available_holidays') + " h");
            Ext.getCmp('used_holidays').setValue(summaryStore.getAt(0).get('used_holidays') + " h");
            Ext.getCmp('scheduled_holidays').setValue(summaryStore.getAt(0).get('scheduled_holidays') + " h");
            Ext.getCmp('pending_holidays').setValue(summaryStore.getAt(0).get('pending_holidays') + " h");
            Ext.getCmp('acc_extra_hours').setValue(summaryStore.getAt(0).get('acc_extra_hours') + " h");
        },
    }
});

/* Proxy to the services related with load/save tasks */
var tasksServiceProxy = new Ext.data.HttpProxy({
    method: 'POST',
    api: {
        read    : {url: 'services/getUserTasksService.php', method: 'GET'},
        create  : 'services/createTasksService.php',
        update  : 'services/updateTasksService.php',
        destroy : 'services/deleteTasksService.php'
    },
});

/* Store to load/save tasks */
var tasksStore = new Ext.ux.TasksStore({
    autoLoad: true,  //initial data are loaded in the application init
    autoSave: false, //if set true, changes will be sent instantly
    baseParams: {
        'login': user,
        'date': dateString,
        'dateFormat': 'Y-m-d',
    },
    storeId: 'id',
    proxy: tasksServiceProxy,
    reader:new Ext.data.XmlReader({
        record: 'task',
        successProperty: 'success',
        idProperty:'id'
        }, taskRecord),
    writer:new Ext.data.XmlWriter({
        xmlEncoding: 'UTF-8',
        writeAllFields: false,
        tpl: '<' + '?xml version="{version}" encoding="{encoding}"?' + '>' +
            '<tpl if="records.length &gt; 0">' +
                '<tpl if="root">' +
                    '<tasks>' +
                        '<tpl for="records">' +
                            '<task>' +
                                '<date>' + dateString + '</date>' +
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
            Ext.onReady(function () {
                if (options.add == true) {
                    for (i in records) {
                        if (i >= 0) {
                            records[i].markDirty();
                            records[i].id = null;
                            records[i].phantom = true;
                            records[i].data['date'] = dateString;
                            store.remove(records[i]);
                            store.add(records[i]);
                            var r = records[i];
                            taskPanel = new TaskPanel({
                                parent: tasksScrollArea,
                                store: tasksStore,
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
                            store: tasksStore,
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

                    addNewTaskIfEmpty();
                }
            }, this /* scope inside onReady = this (the store object) */);
        },

        'save': function () {
            // We don't wrap this inside Ext.onReady, because it's not possible for
            // users to trigger this operation before the page is fully interactive
            if (!tasksStore.error) {
                Ext.getCmp('status_display').setText("Status: saved at "+ new Date());
                if(tasksStore.showConfirmation) {
                    App.setAlert(true, "Task Records Changes Saved");
                }
                summaryStore.load();
            }
            tasksStore.error = false;
        },
        'exception': function(proxy, type, action, eOpts, res) {
            Ext.onReady(function () { // this may run in case of error in the initial load
                let parser = new DOMParser();
                let errorDoc = parser.parseFromString(res.responseText, "text/xml");
                let errorMessage = "";
                for (error of errorDoc.getElementsByTagName("error")) {
                    errorMessage += error.childNodes[0].nodeValue + "\n";
                }
                App.setAlert(false, errorMessage);
                tasksStore.error = true;
            });
        },
        'update': function () {
            Ext.getCmp('status_display').setText("Status: Pending changes");
        },
        'remove': function () {
            Ext.getCmp('status_display').setText("Status: Pending changes requiring manual save");
        }
    }
});

Ext.onReady(function(){

    Ext.QuickTips.init();

    /* Container for the TaskPanels (with scroll bars enabled) */
    tasksScrollArea = new Ext.Container({autoScroll:true,  renderTo: 'tasks'});

    // Validate the inputs in the task panel
    function validateTasks() {
        var panels = tasksScrollArea.items;
        let errorMessage = "";
        for(var panel=0; panel<panels.getCount(); panel++) {
            if (!panels.get(panel).initTimeField.isValid()
                || !panels.get(panel).endTimeField.isValid()
                || !panels.get(panel).projectComboBox.isValid()) {
                    if(!panels.get(panel).initTimeField.isValid()){
                        errorMessage += "Start time invalid. "
                    }
                    if(!panels.get(panel).endTimeField.isValid()){
                        errorMessage += "End time invalid. "
                    }
                    if(!panels.get(panel).projectComboBox.isValid()){
                        errorMessage += "Project invalid. "
                    }
                return { validated: false, message: errorMessage };
            }
        }
        //check to make sure times are not overlapping
        let check = checkTaskForOverlap(tasksStore);

        if(check.overlapsOneOrMoreExistingTasks){
            return { validated: false, message: check.message}
        }

        return { validated: true, message: "Tasks valid"};
    }

    function saveTasks(showConfirmation) {
        let validation = validateTasks();
        if (validation.validated) {
            tasksStore.showConfirmation = showConfirmation;
            tasksStore.save()
        } else if (showConfirmation)
            // Print error message only if save action was explicit
            App.setAlert(false, validation.message);
    }

    // Callback for save buttons in the UI
    function saveButtonClicked() {
        saveTasks(true);
    }

    // Implement autosave of data, when valid contents are typed in to the task fields
    window.setInterval(function () {
        if(isUnsaved()) {
            saveTasks(true);
        }
    }, 30000);

    /* Build a calendar on the auxiliar sidebar */
    new Ext.Panel({
        renderTo: Ext.get("calendarpanel"),
        items: [
            new Ext.ux.DatePickerPlus({
                allowMouseWheel: false,
                showWeekNumber: true,
                multiselection: false,
                customLinkUrl: 'tasks.php?date=',
                selectedDates: [currentDate],
                value: currentDate,
                startDay: 1,
                listeners: {
                    'select': function (item, selectedDate) {
                        navigateToDate(selectedDate);
                    }
                }
            }),
        ],
    });

    // Cloning Panel
    var cloningPanel = new Ext.FormPanel({
        layout: 'hbox',
        header: false,
        items: [
            {
                name: 'cloneDate',
                id: 'cloneDate',
                hideLabel: true,
                flex: 3,
                xtype: 'datefieldplus',
                format: 'd/m/Y',
                value: lastTaskDate,
                allowBlank: false,
                disabled: forbidden,
            }, new Ext.Button({
                text:'Copy',
                margins: "0px 0 0 0px",
                flex: 1,
                disabled: forbidden,
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

                        removeFreshEmptyTask();

                        // We load that day's tasks and append them into tasks' store
                        tasksStore.load({
                            params: {'date': dateString},
                            add: true,
                        });
                    }
                }
            }),
    ]});


    // Summary Panel
    var userWorkSummaryTitleStyle = 'font-weight: bold; width:190px; padding: 0px;';
    var summaryPanel = new Ext.FormPanel({
        width: 204,
        labelWidth: 100,
        renderTo: Ext.get('summarypanel'),
        frame:true,
        title: 'User Work Summary',
        defaults: {
            labelStyle: 'padding: 1px 0 0',
            style: 'text-align: right; padding: 1px 0 0',
        },
        tools: [{
            id: 'help',
            qtip: 'User work summary documentation',
            handler: function(){
                window.open('../help/user/tasks.html#user-work-summary');
            }
        }],
        defaultType:'displayfield',
        items: [{
            id:'worked_title',
            name: 'worked_title',
            labelSeparator: '',
            labelStyle: userWorkSummaryTitleStyle,
            fieldLabel:'Worked hours',
        },{
            id:'day',
            name: 'day',
            fieldLabel:'Today',
        },{
            id:'week',
            name: 'week',
            fieldLabel:'Week',
        },{
            id:'weekly_goal',
            name: 'weekly_goal',
            fieldLabel:'Week goal',
        },{
            id:'extra_title',
            name: 'extra_title',
            labelSeparator: '',
            labelStyle: userWorkSummaryTitleStyle,
            fieldLabel:'Extra hours',
        },{
            id:'extra_hours',
            name:'extra_hours',
            fieldLabel: 'Year',
        },{
            id:'acc_extra_hours',
            name:'acc_extra_hours',
            fieldLabel:'Total',
        },{
            id:'holidays_title',
            name: 'holidays_title',
            labelSeparator: '',
            labelStyle: userWorkSummaryTitleStyle,
            fieldLabel:'Vacation days',
        },
        {
            id:'available_holidays',
            name:'available_holidays',
            fieldLabel:`Available for ${new Date().getFullYear()}`,
        },
        {
            id:'used_holidays',
            name:'used_holidays',
            fieldLabel:'Used',
        },
        {
            id:'scheduled_holidays',
            name:'scheduled_holidays',
            fieldLabel:'Scheduled',
        },{
            id:'pending_holidays',
            name:'pending_holidays',
            fieldLabel:'Pending',
        }
        ]
    });

    // Expand/collapse all Panel
    var expandButton = new Ext.Button({
        text:'Expand all',
        flex: 2,
        handler: function() {
            var panels = tasksScrollArea.items;
            for(var i=0; i<panels.getCount(); i++) {
                panels.get(i).expand();
            }
            cookieProvider.set('tasksCollapsed', false);
        }
    });
    var collapseButton = new Ext.Button({
        text:'Collapse all',
        flex: 2,
        handler: function() {
            var panels = tasksScrollArea.items;
            for(var i=0; i<panels.getCount(); i++) {
                panels.get(i).collapse();
            }
            cookieProvider.set('tasksCollapsed', true);
        }
    });

    var expandCollapseAllPanel = new Ext.Panel({
        layout: 'hbox',
        items: [collapseButton, expandButton],
    });

    // Templates Panel
    var templatesPanel = new Ext.Panel({
        id: 'templatesPanel',
        defaults: {
            width: '100%',
        },
        items: [
            // Default button for full-day task
            new Ext.Button({
                id: 'fullDayTaskButton',
                text: 'Full-day task',
                disabled: forbidden,
                handler: function () {
                    // Delay task creation until store gets loaded or it won't be properly added
                    if (!isLoaded()) {
                        window.setTimeout(Ext.getCmp('fullDayTaskButton').handler, 100);
                        return;
                    }
                    if (currentJourney == 0)
                        return;

                    removeFreshEmptyTask();

                    // Create task and fill init and end times
                    var newTask = new taskRecord();
                    var init = new Date();
                    init.setHours(0, 0, 0, 0);
                    var end = new Date();
                    end.setHours(0, currentJourney*60, 0, 0);
                    newTask.set('initTime', init.format('H:i'));
                    newTask.set('endTime', end.format('H:i'));

                    // Add record to store and show in a panel
                    var taskPanel = addTask(newTask);

                    // Put the focus on the project field
                    taskPanel.projectComboBox.focus();
                }
            }),
            // Default button for full-holiday task
            new Ext.Button({
                id: 'fullHolidayTaskButton',
                text: 'Full vacation day task',
                disabled: forbidden,
                handler: function () {
                    // Delay task creation until store gets loaded or it won't be properly added
                    if (!isLoaded()) {
                        window.setTimeout(Ext.getCmp('fullHolidayTaskButton').handler, 100);
                        return;
                    }
                    if (currentJourney == 0)
                        return;

                    removeFreshEmptyTask();

                    // Create task and fill init and end times
                    var newTask = new taskRecord();
                    var init = new Date();
                    init.setHours(0, 0, 0, 0);
                    var end = new Date();
                    end.setHours(0, currentJourney*60, 0, 0);
                    newTask.set('initTime', init.format('H:i'));
                    newTask.set('endTime', end.format('H:i'));
                    newTask.set('projectId', vacationsProjectId);

                    // Add record to store and show in a panel
                    var taskPanel = addTask(newTask);
                    // Autosave
                    window.setTimeout(function () {
                        if(isUnsaved()) {
                            saveTasks(true);
                        }
                    }, 500);
                }
            }),
            // Default button for zero hours day task
            new Ext.Button({
                id: 'zeroDayTaskButton',
                text: 'Zero Hours Day',
                disabled: forbidden,
                handler: function () {
                    // Delay task creation until store gets loaded or it won't be properly added
                    if (!isLoaded()) {
                        window.setTimeout(Ext.getCmp('zeroDayTaskButton').handler, 100);
                        return;
                    }
                    if (currentJourney == 0)
                        return;

                    removeFreshEmptyTask();

                    // Create task and fill init and end times
                    let newTask = new taskRecord();
                    let init = new Date();
                    init.setHours(0, 0, 0, 0);
                    let end = init;
                    newTask.set('initTime', init.format('H:i'));
                    newTask.set('endTime', end.format('H:i'));
                    newTask.set('projectId', vacationsProjectId);

                    // Add record to store and show in a panel
                    let taskPanel = addTask(newTask);
                    // Autosave
                    window.setTimeout(function () {
                        if(isUnsaved()) {
                            saveTasks(true);
                        }
                    }, 500);
                }
            }),
        ],
        addButtonForTemplate: function (templateValues) {
            var createButton = new Ext.Button({
                text: ((templateValues['name'] != undefined) &&
                        (templateValues['name'] != '')) ?
                    templateValues['name'] :
                    'Template',
                flex: 3,
                disabled: forbidden,
                handler: function () {
                    // Delay task creation until store gets loaded or it won't be properly added
                    if (!isLoaded()) {
                        window.setTimeout(createButton.handler, 100);
                        return;
                    }

                    removeFreshEmptyTask();

                    // Create and populate a record
                    var newTask = new taskRecord();
                    newTask.set('projectId', templateValues['projectId']);
                    newTask.set('ttype', templateValues['ttype']);
                    newTask.set('story', templateValues['story']);
                    newTask.set('text', templateValues['text']);
                    newTask.set('initTime', templateValues['initTime']);
                    newTask.set('endTime', templateValues['endTime']);
                    // For a fresh template, the templateValue of bool fields return '1'
                    if( templateValues['telework'] == '1' || templateValues['telework'] == 'true' ) {
                        newTask.set('telework', 'true');
                    }
                    if( templateValues['onsite'] == '1' || templateValues['onsite'] == 'true' ) {
                        newTask.set('onsite', 'true');
                    }

                    // Add record to store and show in a panel
                    var taskPanel = addTask(newTask);

                    //put the focus on the init time field
                    taskPanel.initTimeField.focus();
                },
            });
            var deleteButton = new Ext.Button({
                text: 'Delete',
                flex: 1,
                handler: function () {
                    Ext.Msg.confirm("Confirmation", "Are you sure you want to delete this template?", function(btnText){
                        if(btnText == "yes"){
                            //remove from the store
                            store = Ext.StoreMgr.get('templatesStore')
                            store.remove(store.getById(templateValues['id']));
                            //remove from the panel
                            var row = this.findParentByType('panel');
                            var panel = row.findParentByType('panel');
                            panel.remove(row);
                        }
                    }, this)
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

    var newTaskButton = new Ext.Button({
        text:'New',
        flex: 2,
        handler: newTask,
        disabled: forbidden,
    });
    var saveButton = new Ext.Button({
        text:'Save',
        flex: 2,
        handler: saveButtonClicked,
        disabled: forbidden,
    });

    var actionsPanel = new Ext.Panel({
        width: 204,
        renderTo: Ext.get('actionspanel'),
        frame:true,
        title: 'Actions',
        defaults: {
            style: 'padding-bottom: 6px',
        },
        items: [
            new Ext.form.Label({
                text: 'Tasks'
            }),
            new Ext.Panel({
                layout: 'hbox',
                items: [newTaskButton, saveButton],
            }),

            new Ext.form.Label({
                text: 'Panels'
            }),
            expandCollapseAllPanel,

            new Ext.form.Label({
                text: 'Copy tasks from date'
            }),
            cloningPanel,

            new Ext.form.Label({
                text: 'Templates'
            }),
            templatesPanel
        ],
    });

    var bottomPanel = new Ext.Toolbar({
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
                handler: saveButtonClicked,
                disabled: forbidden,
            }), '-',
            {
                xtype: 'tbtext',
                id: 'status_display',
                text: 'Status: No changes detected'
            }, '->',
            new Ext.Button({
                text:'Previous date',
                handler: navigateToPrevDay,
            }), '-',
            new Ext.Button({
                text:'Next date',
                handler: navigateToNextDay,
            }),
        ],
    });

    //hotkeys
    new Ext.KeyMap(document, {
        key: 's',
        ctrl: true,
        stopEvent: true,
        handler: saveButtonClicked,
    });
    new Ext.KeyMap(document, {
        //alternate shortcut for Epiphany
        key: 's',
        alt: true,
        stopEvent: true,
        handler: saveButtonClicked,
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
        alt: true,
        shift: true,
        stopEvent: true,
        handler: function (key, event) {
            var i = key - 49; //49 is the key code for '1'

            tasksScrollArea.items.get(i).initTimeField.focus();
            //TODO: fix problem with blur event, see: http://stackoverflow.com/questions/8656165/combo-doesnt-blur-when-manually-shifting-focus
        }
    });

    summaryStore.load();

    // Navigate on key left and right arrow press
    function checkKeyAndNavigate(e) {
        // We do not want navigation working while a user is editing a task
        if( document.activeElement.tagName != "INPUT" && document.activeElement.tagName != "TEXTAREA" ) {
            switch (e.keyCode) {
                case 37:
                    navigateToPrevDay();
                    break;
                case 39:
                    navigateToNextDay();
                    break;
            }
        }
    }

    // Add handler to check keyboard left/right clicks and change current date
    document.onkeydown = checkKeyAndNavigate;

});
