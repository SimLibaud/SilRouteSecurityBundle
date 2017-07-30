<?php
/**
 * User: Simon Libaud
 * Date: 19/03/2017
 * Email: simonlibaud@gmail.com.
 */
namespace Sil\RouteSecurityBundle\Security;

use Sil\RouteSecurityBundle\Exception\LogicException;
use Sil\RouteSecurityBundle\Interfaces\NamingStrategyInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AccessControl.
 */
class AccessControl
{
    private $router;
    private $routeToRoleConverter;
    private $is_access_control_enable;
    private $secured_routes;
    private $secured_routes_format;
    private $ignored_routes;
    private $ignored_routes_format;

    public function __construct(RouterInterface $router, NamingStrategyInterface $routeToRoleConverter, $configuration)
    {
        $this->router = $router;
        $this->routeToRoleConverter = $routeToRoleConverter;

        $this->is_access_control_enable = $configuration['enable_access_control'];
        $this->secured_routes = $configuration['secured_routes'];
        $this->secured_routes_format = $configuration['secured_routes_format'];
        $this->ignored_routes = $configuration['ignored_routes'];
        $this->ignored_routes_format = $configuration['ignored_routes_format'];
    }

    /**
     * Verify if  user has access to a specific route.
     *
     * @param UserInterface $user
     * @param string        $route
     *
     * @return bool
     */
    public function hasUserAccessToRoute(UserInterface $user, $route)
    {
        if (false === $this->is_access_control_enable || false === $this->isRouteSecure($route)) {
            return true;
        }

        $role = $this->routeToRoleConverter->generateRoleForRoute($route);

        return in_array($role, $user->getRoles());
    }

    /**
     * Verify if user has access to all routes.
     *
     * @param UserInterface $user
     * @param array         $routes
     *
     * @return bool
     */
    public function hasUserAccessToRoutes(UserInterface $user, $routes)
    {
        foreach ($routes as $route) {
            if (false === $this->hasUserAccessToRoute($user, $route)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verify if user has access to one of routes.
     *
     * @param UserInterface $user
     * @param $routes
     *
     * @return bool
     */
    public function hasUserAccessAtLeastOneRoute(UserInterface $user, $routes)
    {
        foreach ($routes as $route) {
            if (true === $this->hasUserAccessToRoute($user, $route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the given route is manage by the bundle depending of the configuration.
     *
     * @param string $route
     *
     * @return bool
     */
    public function isRouteSecure($route)
    {
        $all_secured_routes = [];
        $configured_routes = array_keys($this->router->getRouteCollection()->all());

        if (false === in_array($route, $configured_routes)) {
            throw new LogicException(sprintf('The route %s is not defined in your routing.', $route));
        }

        foreach ($configured_routes as $configured_route) {

            // Ignored routes
            if (in_array($configured_route, $this->ignored_routes)) {
                continue;
            }

            // Ignored routes format
            if (null !== $this->ignored_routes_format && 1 === preg_match($this->ignored_routes_format, $configured_route)) {
                continue;
            }

            // Secured routes
            if (true === in_array($configured_route, $this->secured_routes)) {
                $all_secured_routes[] = $configured_route;
                continue;
            }

            // Secured routes format
            if (null !== $this->secured_routes_format && 1 === preg_match($this->secured_routes_format, $configured_route)) {
                $all_secured_routes[] = $configured_route;
                continue;
            }
        }

        return in_array($route, $all_secured_routes);
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->is_access_control_enable;
    }
}
