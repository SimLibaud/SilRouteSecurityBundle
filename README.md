[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SimLibaud/SilRouteSecurityBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SimLibaud/SilRouteSecurityBundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/SimLibaud/SilRouteSecurityBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/SimLibaud/SilRouteSecurityBundle/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/SimLibaud/SilRouteSecurityBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SimLibaud/SilRouteSecurityBundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/fbed9290-6c11-4461-b386-cf0cb46fc43e/mini.png)](https://insight.sensiolabs.com/projects/fbed9290-6c11-4461-b386-cf0cb46fc43e)

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

`composer require sil/route-security-bundle`

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

#### Enable access control

By default, the access control is disable.

```yaml
sil_route_security:
    enable_access_control: true
```
  
#### List of secured routes

You can define a list of secured routes :

```yaml
sil_route_security:
    secured_routes: [app_user_list, app_user_add]
```

#### Secured routes format

In addition or instead of secured routes list, 
you can define a regex format to configure a set of routes to be secured :

```yaml
sil_route_security:
    secured_routes_format: "/^app_/"
```

#### List of ignored routes

You can define a list of ignored routes :

```yaml
sil_route_security:
    ignored_routes: [app_home, app_login, app_logout]
```

#### Ignored routes format

In addition or instead of ignored routes list, 
you can define a regex format to configure a set of routes to be not secured :

```yaml
sil_route_security:
    ignored_routes_format: "/^app_[a-z]*_api/"`
```

#### Naming strategy

By default, to generate a role for route, the bundle convert the route name to ROLE_.strtoupper($route_name). 
If you want a different format, you can make your hown converter. 
Just create a service that implement the NamingStrategyInterface and configure the bundle option with your service identifier.

```yaml
sil_route_security:
    naming_strategy: my_own_route_to_role_converter
```

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
{% if hasUserAccessToRoutes(app.user, ['app_user_add', 'app_user_edit']) %}
  <a href="{{ path('app_user_add') }}">Add user</a>
  <a href="{{ path('app_user_edit') }}">Edit user</a>
{% endif %}
```

# Access denied behavior

When user access to secured route and does not have the right, an `AccessDeniedException` is throw. The framework will convert it to a 403 response.
Just before that, the event `AccessDeniedToRouteEvent` is dispatch. 
You can listen it and implement your hown behaviour, logging the action for exemple, return a custom response, redirect, whatever...
