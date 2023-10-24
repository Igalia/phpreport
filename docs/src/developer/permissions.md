# Application Roles and Permissions

## Concepts

### Scope

A scope is a concept used to specify access privileges. They are often used in access tokens. We are using them here for authorization to perform actions inside the application. We have constructed scopes in the following way:

`{object/area}:{action}-{action scope}`

For example, `task:create-own` is the scope we use to delineate whether a user has permission to create tasks for themselves (i.e., tasks they own). As noted in the permission matrix below, sometimes a user will have permission to perform CRUD operations for items they own, but not for other items of the same type.

The following is a chart of available objects/areas, actions, and action scopes used in the application. If no additional action scope is specified, it is because there are no owners for that object type; lookup tables - such as task_type - are good examples of types that will have no action scope specified.

> #### Action scopes used
>
> In this application, we define our action scopes this way:
>
> **own**: object/area a user owns, e.g., a task one creates for oneself or a project where the user is project manager
>
> **global**: unowned item global to all users, e.g., global templates
>
> **assigned**: item user is assigned to or associated with, but doesn't own. An example would be a project a user is assigned to.
>
> **other**: action scope that allows a user to access an owned item that are not associated with. A 'human resources' user, for instance, might need to be able to create a long leave for another user or add their capacity (hours/week) for a time period. We also might want to allow any user to see when other users are on vacation for planning purposes.

| Object/Area    | Action                       | Action scopes        |
| -------------- | ---------------------------- | -------------------- |
| task           | create, read, update, delete | own, other           |
| template       | create, read, update, delete | own, global          |
| task_type      | create, read, update, delete |                      |
| project        | create, read, update, delete | own, assigned, other |
| client         | create, read, update, delete |                      |
| project_status | create, read, update, delete |                      |
| dedication     | create, read, update, delete | own, other           |
| user_capacity  | create, read, update, delete | own, other           |
| long_leave     | create, read, update, delete | own, other           |
| vacation       | create, read, update, delete | own, other           |
| config         | create, read, update, delete |                      |

#### Some examples

`template:create-global` gives permission to a user to create global templates (for use by all users), which is different than `template:create-self` which allows creation of a template for oneself.

`vacation:read-other` gives permission for a user to view any other user's vacation.

### Role

A role is simply a set of scopes. It is a quick and convenient way to assign a set of scopes to a user.

There are several predefined roles for the application:

- Staff
- Manager
- Admin
- Human Resources
- Project Manager

You may set these roles in your OAuth provider or use the application database. If you prefer to use the local database, please make sure that the roles above are added to the `user_group` table. You assign a user to a role by creating a row in the `belongs` table with the user's id and the group id of your role. If you prefer to set the roles in your OAuth provider instead, please make sure to set `USE_OIDC_ROLES` to `true` and also pass the roles back on the access token at the root level. You will also need to set the `OIDC_ROLES_PROPERTY` in the .env to the property name on the access token object that will contain the roles (e.g. `OIDC_ROLES_PROPERTY="roles"`).

You can also choose to use your own roles. Just make sure to add the proper sets of scopes to these roles.

## Matrix and Legend

**Legend**

✅ = allowed

⚠️ = partially allowed

For instance, a Staff user can see project details for projects to which they have been assigned, but not for other projects. Or a Staff user can create vacation for themselves, but not others.

⛔️ = not allowed

<div style="width: 100%;">
  <img src="i/matrix.svg" style="width: 100%;" alt="Table of permissions and roles">
</div>
