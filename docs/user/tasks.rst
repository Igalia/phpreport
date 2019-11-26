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

* User work summary: contains useful information about the user work status.

* Calendar: indicates the selected date and you can click on it to go to a
  different date.

* Actions: create new task and save changes, expand and collapse all the panels,
  copy tasks from another date and use templates.

In the central area, you will see the tasks stored in the currently selected
date, one panel per task. All panels have the same components, some are used to
fill the information about the task:

* Time: fill the start and end dates in.

* Project: project the task belongs to. You may search by the project or the
  customer name. There is one special entry in the combo box, *Load all
  projects*, that would let you load the complete list of projects in the
  system; users will only see projects they are assigned to by default.

* Story: you can fill this field with a keyword to help you to differentiate
  tasks inside the same project.

* Description: the big text area in the center can be used to write a
  description of the task.

* Task type: select one of the types for additional info about the task.

* TaskStory: when using XP tracker for a project, this field is used to relate
  the task with a category inside an iteration.

* Telework: flag to differentiate telework tasks.

* Onsite: flag to differentiate on-site tasks.

Actions in task panels:

* Delete: delete the task. Remember to save to do the actual deletion.

* Clone: creates a new task with the same data and empty times.

* Template: create a template based on that task.

* Collapse: the arrow icon in the top-right corner collapses the panel; it's
  useful to save vertical space when there are many tasks.

Below the list of tasks in the central area, there are two additional buttons
for *New task* and *Save changes*, for easy tab-navigation. There are another
two quick-access buttons to the next and previous dates.

Managing tasks
==============

When you open the screen, the current day will be selected; if you are about to
fill tasks for a different date select it from the calendar on the left. Be
careful, if there are unsaved changes, they will be lost when you select a
different date.

Fill the data of the empty task that pops up by default, and add any number of
new tasks using one of the two *New task* buttons.
There are more ways to create new tasks: using the clone button in a different
task, using a template button, or copying from another date.
Repeat as many times as you want until you have filled all the tasks of the day,
changes will be auto-saved every some seconds, there is a message in the bottom
bar that will let you know.

You can delete a task pressing the *Delete* button inside the task panel; the
panel disappears but the deletion won't be saved until you do it manually with
any of the two *Save* buttons.

Be careful with the start and end times of the dates, because they can't overlap;
if they do, tasks won't be saved until you correct the problem and save again.

Using templates
===============

Templates are useful when you create tasks with very similar data very often.
They are listed in the *Templates* section, in the bottom of the left column.
To create a new task from a template, click on the button with
the name of the template.

To add new templates to this list, first you have to create a task; fill it in
with your data, put in *optional* description in the Task description box, and
click on the *template* button. You will be asked to provide a **name** for the template,
which will add up immediately in your templates deck on the left side.You don't
have to fill the start and end times in because they won't be saved in the template.

To delete a template, you only have to press the *Delete* button right to the
button with template name in the list.

There are two default templates that cannot be deleted:

* *Full day task* will create a task and set its start and end times so it
  fills one full day of work. The values will be set according to the users'
  journey values.
* *Full-holiday task* works like the previous one, but will also set the
  project field to 'Holidays'.

Copying tasks from another date
===============================

A common scenario is having a person working in the same tasks for some days,
usually with the same timetable. The copy feature is useful in this case.

In the actions box from the left column, there is a section named 'Copy tasks
from date' containing a date selector and a 'Copy' button.
When you press that button, the
tasks saved in the indicated date will be copied to the current date. Take into
account that copied tasks aren't saved until you press the save button.

The default date to copy from is the last date containing any recorded tasks,
that's the most common use case. Finally, we must clarify that copied tasks can
be modified without affecting the original ones.

Blocked tasks
=============

Sometimes users won't be able to modify or delete some saved tasks, nor even
create new ones on certain dates. It can happen because of two reasons:

* Managers have closed a project. In that case, tasks assigned to that project
  cannot be altered.

* Managers have blocked task reports for the selected date. In this case,
  even the *New task* and *Save changes* buttons would be blocked.

User work summary
=================

The *User work summary* panel on the left contains the following information:

* **Worked (today):** Number of hours worked today.

* **Worked (week):** Number of hours worked this week.

* **Goal (week):** Goal for the selected week.

  This value is calculated taking into account the users' journey values and
  the amount of worked hours so far.

  By default, it represents **the number of hours users must work per week
  to finish the year with zero accumulated hours in that period.**

  For example, if you have worked some extra hours in the last weeks the
  weekly goal will become lower than your weekly schedule. This way you will
  have a way to know how to handle your time and finish the year with zero
  accumulated hours.

  Managers can customize per-user weekly goals with other time periods and
  amounts of hours different of zero. Read more about this in the
  `Setting user custom goals <users-management.rst#setting-user-custom-goals>`__
  section.

* **Extra (year):** Number of extra hours corresponding to the current year.

* **Pending holidays (year):** Number of hours of holidays pending to be enjoyed this year.

* **Extra (total):** Number of historically accumulated extra hours.

Hotkeys
=======

Some hotkeys are available to increase the productivity for keyboard users:

CTRL + S
  Save changes. Some browsers don't allow to overwrite the behaviour of this
  key combination (e.g. Epiphany), so you can use ALT + S instead.

CTRL + N
  Create a new task. The cursor will be placed in the first field of the new
  task. Some browsers don't allow to overwrite the behaviour of this
  key combination (e.g. Chrome), so you can use ALT + N, CTRL + U or ALT + U
  instead.

CTRL + number
  The cursor will be placed in the first field of the first, second, third, etc.
  task depending on the number you pressed.

TAB
  Advance to the next field.

SHIFT + TAB
  Go back to the previous field.

LEFT, RIGHT
  Jump to the previous or next date, when keyboard focus is not inside a field.
