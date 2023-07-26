# User web interface

The user web interface is mostly implemented in JavaScript, using the
Ext JS 3.x library. There are some small parts written directly in HTML
and PHP, which cover the basic UI blocks and access control,
respectively.

## Page structure

Every page in PhpReport is structured in a set of predefined areas:
header, menu bar, content area and footer:

- The header must be included from the `web/include/header.php` file.
  It contains the generic includes of JS libraries and CSS files, and
  defines the page header where the application name is shown.
- The menu bar contains the application menu and is included from
  `web/include/menubar.php`.
- The content area is where the contents of the particular PhpReport
  screen are rendered. It usually contains a `<div>` area with the
  `content` id, but it is completely free. It is implemented by every
  page, usually in two different files. For example, the tasks screen
  is implemented by `web/tasks.php` and `web/js/tasks.js`.
- The footer is the bottom area from the page and must be included
  from the `web/include/footer.php` file. It closes the HTML tags
  opened by the header.

## Access control

The access control to the application is implemented at page level,
using a configuration file and the utility class _LoginManager_ written
in PHP.

When accessing to the application, the initial page requests a name of
user and password; with these data, calls the method _login_ of
_LoginManager_. This method checks the credentials of the user calling
the _login_ operation of the model layers through the users facade
object and, in case of success, it stores the user object in the PHP
session.

From now on, every page has to run two operations before beginning:

- In the first place, has to invoke to the method _isLogged_ of
  _LoginManager_ to know if the user has already initiated session in
  the application. For this, the method checks the data in the session
  and if there is already a user, as it should have been stored by the
  _login_ operation, it continues the process.
- The second step is to invoke to the method _isAllowed_ of
  _LoginManager_. This method queries a configuration file that keeps
  the relations between user groups and the pages of the application
  to which they have access. Checking the groups to which the current
  user belongs, it will know if the process of loading the current
  page may continue.

Because the process above happens for every page (excepting login
pages), it is implemented in `/web/auth.php` which is included from
every page.

## Relation with other application layers

It has already been commented, the user web interface is related both
with the layer below, the business logic layer, and with the web service
layer. It may use the former for synchronous operation and the latter
for asynchronous operations. The next sequence diagram illustrates the
access control implementation as we have just explained:

![](i/web-login-sequence.png)

On the one hand, some screens may directly use the PHP code from the
layer below. This happens when asynchronous interaction has no value.
For example, the login screen: the user has to fill up all the data,
send them and wait for an answer, which may be an error message or a
redirect to inside the application. This has been developed with an HTML
form and a small block of PHP code that calls the _login_ function of
the business logic layer. In the sequence diagram it can be seen how the
process of the login operation bypasses the web services layer.

On the other hand, in the majority of the cases the interaction happens
using the web services layer as an intermediary. For example, these are
the steps that comprise the creation of a task:

- An user creates a task and fills up its data. This happens in the
  browser, without the server being aware of it. In particular, there
  is an object _Ext.data.Store_ that stores the information of all the
  tasks that are shown on screen.
- When an user presses the _Save_ button, the code of this event
  invokes the method _save()_ of the _Store_. This object is already
  configured to use the URL of the _createTask_ web service, and is in
  charge to build the XML and send it by means of an asynchronous
  request.
- The service _createTask_ transforms the XML received in a _TaskVO_
  object from the application domain and calls the task creation
  method in the tasks facade.
- The service receives the return of the creation method and composes
  an XML with a success or an error message if it was the case. This
  XML is sent back to the client.
- The asynchronous request gets the response it had been waiting from
  the server. This triggers an event that shows the result of the
  operation in screen to the user.
