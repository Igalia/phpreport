Tasks management
################

.. contents::

The first option in PhpReport top menu points to the task management screen. In
this screens users will be able to fill in their tasks, modify or delete them.

.. figure:: i/tasks-menu.png

The tasks management screen
===========================

.. figure:: i/tasks-screen.png

In the left column, you can find:

* User work summary: counts the number of hours worked in the selected day, week
  and month.

* Calendar: indicates the selected date and you can click on it to go to a
  different date.

* Copy tasks: copies the tasks from one date to the currently selected date.

* Actions: create new task and save changes, expand and collapse all the panels,
  and use templates.

In the central area, you will see the tasks stored in the currently selected
date, one panel per task. All panels have the same components:

* Time: fill the start and end dates in.

* Customer: customer financing the task. Selecting one customer will filter the
  projects in the next box to see only those belonging to the customer.

* Project: project the task belongs to.

* Task type: select one of the types for additional info about the task.

* Story: you can fill this field with a keyword to help you to differentiate
  tasks with the same customer and project.

* TaskStory: when using XP tracker for a project, this field is used to relate
  the task with a category inside an iteration.

* Telework: flag to differentiate telework tasks.

* Delete: delete the task. Remember to save to do the actual deletion.

* Clone: creates a new task with the same data and empty times.

* Template: create a template based on that task.

* Description: the big text area on the right can be used to write a description
  of the task.

* Collapse: the arrow icon in the top-right corner collapses the panel; it's
  useful to save vertical space when there are many tasks.

Below the list of tasks in the central area, there are two more icons for *New
task* and *Save changes*.

Managing tasks
==============

When you open the screen, the current day will be selected; if you are about to
fill tasks for a different date select it from the calendar on the left. Be
careful, if there are unsaved changes, they will be lost when you select a
different date.

Create a new task with any of the two *New task* buttons and fill in its data.
There are more ways to create new tasks: using the clone button in a different
task, using a template button, or copying from another date.
Repeat as many times as you want until you have filled all the tasks of the day,
and don't forget to save once you're done using any of the two *Save* buttons.
You can delete a task pressing the *Delete* button inside the task panel; the
panel disappears but the deletion won't be saved until you do it manually with
the *Save* button.

Take care with the start and end times of the dates, because they can't overlap;
if they do, tasks won't be saved until you correct the problem and save again.

Using templates
===============

Templates are useful when you create tasks with very similar data very often.
They are listed in the *Templates* section, in the bottom of the left column.
Creating a new task from a template is as easy as clicking in the button with
the name of the template.

To add new templates to this list, first you have to create a task; fill it in
with your data, and write a name for the template in the description area. You
don't have to fill the start and end times in because they won't be saved in the
template. Once it's ready, press the *Template* button in the panel of the task,
and it will be added to the list.

To delete a template, you only have to press the *Delete* button right to the
button with template name in the list.

Copying tasks from another date
===============================

A common scenario is having a person working in the same tasks for some days,
usually with the same timetable. The copy feature is useful in this case.

In the left column, right below the calendar, there's a panel with a date and
the button 'Copy tasks from selected date'. When you press that button, the
tasks saved in the indicated date will be copied to the current date. Take into
account that copied tasks aren't saved until you press the save button.

Notice that the default date to copy from is the day before the current date;
that's the most common use case. Finally, mention that copied tasks can be
modified without affecting to the original ones.

Blocked tasks
=============

Sometimes users won't be able to modify or delete some saved tasks, nor even
create new ones on certain dates. It can happen because of two reasons:

* Managers have closed a project. In that case, tasks assigned to that project
  cannot be altered.

* Managers have blocked tasks reports older than a certain date: a number of
  days can be set so any tasks older than that cannot be changed. In this case,
  even the *New task* and *Save changes* buttons would be blocked.
