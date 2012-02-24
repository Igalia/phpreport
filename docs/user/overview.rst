Overview
########

.. contents::

PhpReport is an open source web application for time tracking, focused both on
project control and working day management. It tries to provide useful data for
workers and managers about the way time is devoted to projects.


Data model for time tracking
============================

The core purpose of PhpReport is being able to track the time devoted to work by
members of an organization, and which part of that time is devoted to every
running project. The core of the time tracking revolves around these four
entities:

* Task entity represents a task performed by a user in a date, with a determinate
  length stored in its start and end time.

* User entity represents a user of the system, who is meant to be a member of the
  organization being tracked. Every member is supposed to be working and tracking
  his/her hours in the system.

* Project entity represents a project, a common goal for a set of tasks.

* Client entity represents an organization who requests a project and usually
  finances the tasks needed to carry over that project.

.. figure:: i/main-classes.png
   :scale: 50

   Main entities related with time tracking

The relations stablished between them are the following:

* Every task belongs to one user; users can execute any number of tasks.

* Users are assigned to projects, with no constraints of number of users or
  projects.

* Every task belongs to one project; projects can have any number of tasks.

* Clients request projects; multiple clients can request the same project, and
  a client can request more than one project.

* In a scenario where multiple clients have requested the same project, every
  task is related with one specific client who in theory finances the cost of
  that task.
