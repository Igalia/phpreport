Overview
========

::: {.contents}
:::

Architecture
------------

The application is structured in several layers, as displayed in the
following figure:

![](i/architecture.png)

-   The *persistence layer* is implemented by the database system, in
    particular PostgreSQL. Data is modelled following the [relational
    model](https://en.wikipedia.org/wiki/Relational_model) approach.
-   The *data access layer* interfaces the program with the persistence
    layer, translating the data in the database into program objects. It
    isolates the program from the database implementation, theoretically
    allowing the integration of different DB systems.
-   The *business logic layer* implements the business logic and exposes
    it as a set of operations grouped in Facade objects.
-   The *web service layer* exposes the operations from the layer below
    in the network using a
    [REST](https://en.wikipedia.org/wiki/Representational_state_transfer)
    approach.
-   The *user web interface* has been built in a way it can use the
    business logic layer directly for synchronous operation and the
    services in the web service layer for asynchronous operations.
-   *External client applications* may be implemented on top of the web
    service layer.

Directory tree
--------------

The code is structured in the following directories:

-   config: configuration files of the application. Contains
    configuration of user group permissions.
-   docs: contains documentation source files.
    -   admin: documentation for system administrators about
        installation, configuration and upgrade of PhpReport.
    -   developer: documentation for developers.
    -   user: end user documentation explaining all the features of the
        application.
-   help: auto-generated directory for documentation in html format. It
    is not part of the sources but part of the release tarballs.
-   install: complete sources of the installation wizard.
-   model:
    -   dao: *data access objects* of the data access layer.
    -   vo: *value objects* of the data access layer.
    -   facade: sources of the business logic layer.
-   sql: sql sources to generate the basic persistence layer for
    PhpReport 2.0.
    -   update: changes to the basic persistence layer done in later
        releases.
-   test: unit test suite.
-   update: scripts to migrate the application between releases.
-   util: utility source code used along different layers of the
    architecture.
-   web: sources of the user web interface.
    -   ext: contains the entire Ext JS 3.x framework.
    -   include: utility source code for the web interface, imported Ext
        JS widgets and other resources like CSS, images, etc.
    -   js: JavaScript sources of the web interface.
    -   services: source code of the web service layer.
