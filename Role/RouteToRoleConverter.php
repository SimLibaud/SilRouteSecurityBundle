<?php
/**
 * User: Simon Libaud
 * Date: 19/03/2017
 * Email: simonlibaud@gmail.com
 */

namespace Sil\RouteSecurityBundle\Role;

use Sil\RouteSecurityBundle\Interfaces\NamingStrategyInterface;

/**
 * Class RolesGenerator
 *
 * This class convert a route name into role name.
 * You can replace this converter with your own.
 * Make service that implement NamingStrategyInterface and
 * configure 'naming_strategy' option with your service id.
 *
 * @package Sil\RouteSecurityBundl\Role
 */
class RouteToRoleConverter implements NamingStrategyInterface
{

    /**
     * {@inheritdoc}
     */
    public function generateRoleForRoute($route)
    {
        return 'ROLE_'.strtoupper($route);
    }

}