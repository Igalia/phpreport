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

define('PHPREPORT_ROOT', __DIR__ . '/../');

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');
include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');

$user = $_SESSION['user'];

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Tasks");
include("include/header.php");
include("include/sidebar.php");

/* Get the needed variables to be passed to the Javascript code */
if(isset($_GET["date"]))
    $date = $_GET["date"];
else
    $date = date("Y-m-d");

/* Check if the date is enabled to write */
if(!TasksFacade::IsWriteAllowedForDate(new DateTime($date))) {
    echo '<script type="text/javascript">var forbidden = true;</script>';
}
else {
    echo '<script type="text/javascript">var forbidden = false;</script>';
}

?>
<script src="include/ext.ux.datepickerplus/ext.ux.datepickerplus.js"></script>
<script src="include/ext.ux.datepickerplus/ext.ux.datepickerplus-holidays.js"></script>
<script src="js/include/TasksStore.js"></script>
<script type="text/javascript">

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

/* Global variables extracted from the PHP side */
var date = '<?php echo $date?>';
var user = '<?php echo $user->getLogin()?>';

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
    {name:'customerId'},
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
]);

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
                ref: '../length'
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
                store: new Ext.data.Store({
                    parent: this,
                    autoLoad: true,  //initial data are loaded in the application init
                    autoSave: false, //if set true, changes will be sent instantly
                    baseParams: {
                        'login': user,
                        'active': 'true',
                        'order': 'name',
                    },
                    proxy: new Ext.data.HttpProxy({url: 'services/getUserCustomersService.php', method: 'GET'}),
                    reader:new Ext.data.XmlReader({record: 'customer', id:'id' }, customerRecord),
                    remoteSort: false,
                    listeners: {
                        'load': function () {
                            //the value of customerComboBox has to be set after loading the data on this store
                            if ((this.findExact("id", this.parent.taskRecord.data['customerId']) == -1) &&
                                    (this.parent.taskRecord.data['id'] > 0) &&
                                    (this.parent.taskRecord.data['customerId'] > 0)) {
                                //we couldn't find the customer in the list,
                                //because the task belongs to a closed project
                                //we load the list again disabling activation filter
                                this.setBaseParam('active', false);
                                this.load();
                            } else
                                this.parent.customerComboBox.setValue(this.parent.taskRecord.data['customerId']);
                        }
                    },
                }),
                mode: 'local',
                typeAhead: true,
                valueField: 'id',
                displayField: 'name',
                triggerAction: 'all',
                forceSelection: true,
                listeners: {
                    'select': function () {
                        this.parent.taskRecord.set('customerId',this.getValue());
                    },
                    'blur': function () {
                        // workaround in case you set a value, save with ctrl+s,
                        // delete the value and change the focus. In that case,
                        // 'select' or 'change' events wouldn't be triggered.
                        this.parent.taskRecord.set('customerId',this.getValue());

                        //invoke changes in the "Projects" combo box
                        this.parent.projectComboBox.store.setBaseParam('cid',this.parent.taskRecord.data['customerId']);
                        this.parent.projectComboBox.store.setBaseParam('customerChanged', true);
                        this.parent.projectComboBox.store.load();
                    }
                },
            }),
            projectComboBox: new Ext.form.ComboBox({
                parent: this,
                tabIndex: tab++,
                store: new Ext.data.Store({
                    parent: this,
                    autoLoad: true,  //initial data are loaded in the application init
                    autoSave: false, //if set true, changes will be sent instantly
                    baseParams: {
                        'login': user,
                        'cid': this.taskRecord.data['customerId'],
                        'order': 'description',
                        'active': 'true',
                    },
                    proxy: new Ext.data.HttpProxy({url: 'services/getCustomerProjectsService.php', method: 'GET'}),
                    reader:new Ext.data.XmlReader({record: 'project', id:'id' }, projectRecord),
                    remoteSort: false,
                    listeners: {
                        'load': function () {
                            //the value of projectComboBox has to be set after loading the data on this store
                            if ((this.findExact("id", this.parent.taskRecord.data['projectId']) == -1) &&
                                    (this.parent.taskRecord.data['projectId'] > 0)) {
                                if(this.baseParams.customerChanged) {
                                    //the project could not be found because the user
                                    //has just changed the client
                                    this.parent.projectComboBox.setValue(null);
                                    this.parent.taskRecord.set('projectId', null);
                                }
                                else if(this.parent.taskRecord.data['id'] > 0) {
                                    //the project could not be found because it's not
                                    //open, we disable edition for this task and reload
                                    //the list with that only project
                                    this.parent.setReadOnly(true);
                                    this.proxy.setUrl('services/getProjectService.php', true);
                                    this.setBaseParam('pid', this.parent.taskRecord.data['projectId']);
                                    this.load();
                                }
                            } else
                                this.parent.projectComboBox.setValue(this.parent.taskRecord.data['projectId']);
                        }
                    },
                }),
                mode: 'local',
                valueField: 'id',
                typeAhead: true,
                triggerAction: 'all',
                displayField: 'description',
                forceSelection: true,
                listeners: {
                    'select': function () {
                        this.parent.taskRecord.set('projectId',this.getValue());
                    },
                    'blur': function () {
                        // workaround in case you set a value, save with ctrl+s,
                        // delete the value and change the focus. In that case,
                        // 'select' or 'change' events wouldn't be triggered.
                        this.parent.taskRecord.set('projectId',this.getValue());

                        //invoke changes in the "TaskStory" combo box
                        this.parent.taskStoryComboBox.store.setBaseParam('pid',this.parent.taskRecord.data['projectId']);
                        this.parent.taskStoryComboBox.store.load();
                    },
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
            storyField: new Ext.form.TextField({
                parent: this,
                value: this.taskRecord.data['story'],
                tabIndex: tab++,
                enableKeyEvents: true,
                listeners: {
                    'keyup': function () {
                        this.parent.taskRecord.set('story',this.getValue());
                    },
                    'blur': function () {
                        this.setValue(Trim(this.getValue()));
                    }
                }
            }),
            taskStoryComboBox: new Ext.form.ComboBox({
                parent: this,
                tabIndex: tab++,
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
            descriptionTextArea: new Ext.form.TextArea({
                parent: this,
                height: 195,
                anchor: '100%',
                tabIndex: tab++,
                value: this.taskRecord.data['text'],
                enableKeyEvents: true,
                listeners: {
                    'keyup': function () {
                        this.parent.taskRecord.set('text',Trim(this.getValue()));
                    },
                    'blur': function () {
                        this.setValue(Trim(this.getValue()));
                    }
                }
            }),
            deleteButton: new Ext.Button({
                parent: this,
                text:'Delete',
                width: 40,
                tabIndex: tab++,
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
                width: 40,
                tabIndex: tab++,
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
                }
            }),
            createTemplateButton: new Ext.Button({
                parent: this,
                text:'Template',
                width: 40,
                tabIndex: tab++,
                margins: "7px 0 0 5px",
                handler: function() {
                    //get the templates from the cookie
                    var templatesArray = cookieProvider.decodeValue(
                            cookieProvider.get('taskTemplate'));
                    if (templatesArray == undefined) {
                        templatesArray = [];
                    }
                    //add the new template to the array
                    var task = this.parent.taskRecord;
                    var template = [task.get('customerId'),
                                    task.get('projectId'),
                                    task.get('ttype'),
                                    task.get('story'),
                                    task.get('taskStoryId'),
                                    task.get('telework'),
                                    task.get('onsite'),
                                    task.get('text')]
                    templatesArray.push(template);

                    //save the templates into the cookie
                    cookieProvider.set('taskTemplate',
                            cookieProvider.encodeValue(templatesArray));

                    //add the button for the new task to the sidebar panel
                    Ext.getCmp('templatesPanel').addButtonForTemplate(
                            template, templatesArray.length - 1);
                }
            }),
        });

        /* Set the value of the checkboxes correctly */
        this.teleworkCheckBox.setValue((this.taskRecord.data['telework']=='true'));
        this.onsiteCheckBox.setValue((this.taskRecord.data['onsite']=='true'));

        /* Place the subelements correctly into the form */
        leftBox = new Ext.Panel({
            layout: 'anchor',
            width: 230,
            defaults: {width: 225},
            items: [
                new Ext.Container({
                    layout: 'hbox',
                    layoutConfig: {defaultMargins: "0 5px 0 0"},
                    items:[
                        new Ext.form.Label({text: 'Time '}),
                        this.initTimeField,
                        new Ext.form.Label({text: ' - '}),
                        this.endTimeField,
                        this.length,
                    ]
                }),
                new Ext.form.Label({text: 'Customer'}),
                this.customerComboBox,
                new Ext.form.Label({text: 'Project'}),
                this.projectComboBox,
                new Ext.form.Label({text: 'Task type'}),
                this.taskTypeComboBox,
                new Ext.form.Label({text: 'Story'}),
                this.storyField,
                new Ext.form.Label({text: 'TaskStory'}),
                this.taskStoryComboBox,
                new Ext.Container({
                    layout: 'hbox',
                    layoutConfig: {defaultMargins: "7px 5px 0 0"},
                    items: [this.teleworkCheckBox, this.onsiteCheckBox]}),
            ],
        });
        rightBox = new Ext.Panel({
            layout:'anchor',
            monitorResize: true,
            columnWidth: 1,
            items:[
                this.descriptionTextArea,
                new Ext.Container({
                    layout: 'hbox',
                    layoutConfig: {defaultMargins: "7px 5px 0 0"},
                    items:[
                        this.deleteButton,
                        this.cloneButton,
                        this.createTemplateButton,
                    ]
                })
            ],
        });
        this.items = [leftBox, rightBox];

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
    var myStore = new Ext.ux.TasksStore({
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
                if (options.add == true)
                    for (i in records) {
                        if (i >=0) {
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
                                taskRecord:r,
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

                            if(forbidden) {
                                taskPanel.setReadOnly(true);
                            }

                            updateTasksLength(taskPanel);
                        }
                    }
                else this.each(function(r) {
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
            },
            'save': function () {
                if (!myStore.error) {
                    App.setAlert(true, "Task Records Changes Saved");
                    summaryStore.load();
                }
                myStore.error = false;
            },
            'exception': function () {
                App.setAlert(false, "Some Error Occurred While Saving The Changes (please check you haven't clipped working hours)");
                myStore.error = true;
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
            },
        }
    });

    /* Add a callback to add new tasks */
    function newTask() {
        newTask = new taskRecord();
        myStore.add(newTask);
        taskPanel = new TaskPanel({
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

        // We set the current time as end
        var now = new Date();
        taskPanel.endTimeField.setRawValue(now.format('H:i'));
        newTask.set('endTime',now.format('H:i'));
        taskPanel.endTimeField.validate();

        taskPanel.initTimeField.focus();
    }

    /* Add a callback to save tasks */
    function saveTasks() {

        // First we check if the time fields of all records are valid
        var panels = tasksScrollArea.items;
        var valids = true;
        for(var panel=0; panel<panels.getCount(); panel++) {
            if (!panels.get(panel).initTimeField.isValid() || !panels.get(panel).endTimeField.isValid()) {
                valids = false;
                break;
            }
        }

        // If they are so, then we save the changes
        if (valids) {
            myStore.each(function(r) {
                if (r.data['story'] != undefined)
                    r.data['story'] = xmlencode(r.data['story']);
                if (r.data['text'] != undefined)
                    r.data['text'] = xmlencode(r.data['text']);
            });
            myStore.save();
        } else  // Otherwise, we print the error message
          App.setAlert(false, "Check For Invalid Field Values");
    }

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
                xtype: 'datefield',
                format: 'd/m/Y',
                // Default value is previous day
                value: Date.parseDate('<?php
                    $dayBefore = new DateTime($date);
                    echo $dayBefore->sub(new DateInterval('P1D'))->format('Y-m-d');
                        ?>', 'Y-m-d'),
                allowBlank: false,
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
        addButtonForTemplate: function (templateValues, indexInsideCookie) {
            var createButton = new Ext.Button({
                text: ((templateValues[7] != undefined) &&
                        (templateValues[7] != '')) ?
                    templateValues[7] :
                    'Template',
                flex: 3,
                disabled: forbidden,
                handler: function () {
                    //create and populate a record
                    var newTask = new taskRecord();
                    newTask.set('customerId', templateValues[0]);
                    newTask.set('projectId', templateValues[1]);
                    newTask.set('ttype', templateValues[2]);
                    newTask.set('story', templateValues[3]);
                    newTask.set('taskStoryId', templateValues[4]);
                    newTask.set('telework', templateValues[5]);
                    newTask.set('onsite', templateValues[6]);
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
                },
            });
            var deleteButton = new Ext.Button({
                text: 'Delete',
                flex: 1,
                handler: function () {
                    var row = this.findParentByType('panel');

                    //remove from the cookie
                    var templatesArray = cookieProvider.decodeValue(
                            cookieProvider.get('taskTemplate'));
                    templatesArray.splice(row.indexInsideCookie, 1);
                    cookieProvider.set('taskTemplate',
                            cookieProvider.encodeValue(templatesArray));

                    //update indexes of the other templates
                    var sibling = row.nextSibling();
                    while(sibling != null) {
                        sibling.indexInsideCookie -= 1;
                        sibling = sibling.nextSibling();
                    }

                    //remove from the panel
                    var panel = row.findParentByType('panel');
                    panel.remove(row);

                },
            });
            this.add(new Ext.Panel({
                indexInsideCookie: indexInsideCookie,
                layout: 'hbox',
                items: [createButton, deleteButton],
            }));
            this.doLayout();
        },
    });

    // Populate templates panel
    var templatesList = cookieProvider.decodeValue(
            cookieProvider.get('taskTemplate'));
    if (templatesList != undefined) {
        for (var i = 0; i < templatesList.length; i++) {
            var templateValues = templatesList[i];
            templatesPanel.addButtonForTemplate(templateValues, i);
        }
    }

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
            }),
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

});
</script>

<div id="summarypanel" class="auxiliarpanel">
</div>
<div id="calendarpanel" class="auxiliarpanel">
</div>
<div id="actionspanel" class="auxiliarpanel">
</div>

<div id="content" style="margin-left: 215px;">
    <div id="tasks"></div>
    <div id="moreactions"></div>
</div>

<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
