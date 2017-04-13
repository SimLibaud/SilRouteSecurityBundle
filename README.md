# SilRouteSecurityBundle

This bundle provide a way to secure accesses to all routes of your application and adapt the view according to the logged user.

# Principle

* The bundle generate roles for all configured routes.
* The bundle listen the `kernel.request` event and retrieve the requested route.
* If the route is configure to be secured, the bundle check if the current user has the appropriate role. If not, an AccessDeniedException from Symfony security component is throw.
You will see above how to modify this behaviour.

For all routes configured in access control, the user must be authenticated and implement the `UserInterface` of Symfony security component.

# Installation

## Composer

Run the command `composer require sil/route-security-bundle`

## Register the bundle

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Sil\RouteSecurityBundle\SilRouteSecurityBundle(),
        // ...
    );
}
```

# Configuration

You can configure the bundle under the `sil_route_security` key. 

## Options

#### `enable_access_control`

* Enable/disable the access control
* Type : `boolean`
* Default value : `false`
* Exemple : `enable_access_control: true`
  
#### `secured_routes`
  
* List of routes to secure
* Type : `array`.
* Exemple : `secured_routes: [app_user_list, app_user_add]`

#### `secured_routes_format`

* Regex format to configure a set of routes to be secured
* Type : `string`
* Exemple : `secured_routes_format: "/^app_/"`

#### `ignored_routes`

* List of routes that have no access restriction
* Type : `array`
* Exemple : `ignored_routes: [app_home, app_login, app_logout]`

#### `ignored_routes_format`

* Regex format to configure a set of routes that have no access restriction
* Type : `string`
* Exemple : `ignored_routes_format: "/^app_[a-z]*_api/"`

#### `naming_strategy`

* Service identifier for converting the name of a route to a role.
* The service must implement the `NamingStrategyInterface`
* Exemple : `naming_strategy: my_hown_route_to_role_converter`

# Roles provider

All generated roles is accessible through the `sil_route_security.roles_provider` service.

```php
<?php
// Controller
...
$rolesProvider = $this->get('sil_route_security.roles_provider');
$generatedRoles = $rolesProvider->getRoles();
...

```

For exemple, you can inject this service into your `UserFormType` to configure the associates roles to an user.

# Adapt template view

The bundle expose 3 twig functions that allow you to generate view according to the roles of user.

#### `hasUserAccessToRoute`

* Check if the user has access to the given route
* Exemple :

```twig
{% if hasUserAccessToRoute(app.user, 'app_user_add') %}
  <a href="{{ path('app_user_add') }}">Add user</a>
{% endif %}
```

#### `hasUserAccessAtLeastOneRoute`

* Check if the user has access at least to one route
* Exemple : 

```twig
{% if hasUserAccessAtLeastOneRoute(app.user, ['app_user_add', 'app_user_edit']) %}
  <span class="dropdown-title">User management</span>
  <ul class="dropdown-item">    
    {% if hasUserAccessToRoute(app.user, 'app_user_add') %}
      <li>
        <a href="{{ path('app_user_add') }}">Add user</a>
      </li>
    {% endif %}    
    {% if hasUserAccessToRoute(app.user, 'app_user_edit') %}
      <li>
        <a href="{{ path('app_user_edit') }}">Edit user</a>
      </li>
    {% endif %}    
  </ul>
{% endif %}
```

#### `hasUserAccessToRoutes`

* Check if the user has access to all given routes
* Exemple :

```twig
{% if hasUserAccessToRoutes(app.user, ['app_user_add', 'app_user_edit', 'app_user_remove']) %}
  <a href="{{ path('app_user_add') }}">Add user</a>
  <a href="{{ path('app_user_edit') }}">Edit user</a>
{% endif %}
```

# Naming strategy

By default, to generate a role for route, the bundle convert the route name to `ROLE_.strtoupper($route_name)`. 
If you want a different format, you can make your hown converter. Just create a service that implement the `NamingStrategyInterface` and configure the bundle option `naming_strategy` with your service identifier.

# Access denied behavior

When user access to secured route and does not have the right, an `AccessDeniedException` is throw. The framework will convert it to a 403 response.
Just before that, the event `AccessDeniedToRouteEvent` is dispatch. 
You can listen it and implement your hown behaviour, logging the action for exemple, return a custom response, redirect, whatever...

# Todo

* Unit tests
* Form type for generated roles
* Cache mechanism for secure routes ?
