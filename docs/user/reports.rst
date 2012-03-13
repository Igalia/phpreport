Reports
#######

.. contents::

PhpReport provides different reports to check the status of projects and users.
They can be accessed in the *Reports* menu.

.. figure:: i/reports-menu.png

User details
============

.. figure:: i/user-details-screen.png

This report details the work of the logged user between two dates. It shows the
time spent in every project and customer.

In the top of the screen some basic data of the user is shown: its login and
the groups it belongs to. Then there are two date inputs to insert the report
dates: if the start date is left empty, the default value is the date of the
first task inserted by the user; if the end date is empty, the default value is
the current date.

The results are shown in the grid in the central area of the screen; rows
represent projects and columns represent workers. There are two extra columns,
*Total* and *Percentage*; the former shows the total number of hours invested in
a project, independently from the client (thus, it's the sum of all the client
columns) and the latter shows the proportion of hours of one project regarding
all the work done by the user in the same time period.
