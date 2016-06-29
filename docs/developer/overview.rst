Overview
########

.. contents::

Architecture
============

The application is structured in several layers, as displayed in the following
figure:

.. figure:: i/architecture.png

* The *persistence layer* is implemented by the database system, in particular
  PostgreSQL. Data is modelled following the
  `relational model <https://en.wikipedia.org/wiki/Relational_model>`__
  approach.
* The *data access layer* interfaces the program with the persistence layer,
  translating the data in the database into program objects. It isolates the
  program from the database implementation, theoretically allowing the
  integration of different DB systems.
* The *business logic layer* implements the business logic and exposes it as a
  set of operations grouped in Facade objects.
* The *web service layer* exposes the operations from the layer below in the
  network using a
  `REST <https://en.wikipedia.org/wiki/Representational_state_transfer>`__
  approach.
* The *user web interface* has been built in a way it can use the business logic
  layer directly for synchronous operation and the services in the web service
  layer for asynchronous operations.
* *External client applications* may be implemented on top of the web service
  layer.
