<?php
/**
 * User: Simon Libaud
 * Date: 14/03/2017
 * Email: simonlibaud@gmail.com
 */

namespace Sil\RouteSecurityBundle\Role;

use Sil\RouteSecurityBundle\Interfaces\NamingStrategyInterface;

/**
 * Class RolesProvider
 * @package Sil\RouteSecurityBundl\Security
 */
class RolesProvider
{

    private $routeToRoleConverter;
    private $secured_routes;

    /**
     * RolesProvider constructor.
     * @param NamingStrategyInterface $routeToRoleConverter
     * @param array $secured_routes
     */
    public function __construct(NamingStrategyInterface $routeToRoleConverter, $secured_routes)
    {
        $this->routeToRoleConverter = $routeToRoleConverter;
        $this->secured_routes = $secured_routes;
    }

    /**
     * Get roles for secured routes
     *
     * @return array $roles
     */
    public function getRoles()
    {
        $roles = [];
        foreach ($this->secured_routes as $secured_route) {
            $roles[] = $this->routeToRoleConverter->generateRoleForRoute($secured_route);
        }

        return $roles;
    }

}