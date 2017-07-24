<?php
/**
 * User: Simon Libaud
 * Date: 19/03/2017
 * Email: simonlibaud@gmail.com
 */

namespace Sil\RouteSecurityBundle\Interfaces;

/**
 * Interface NamingStrategyInterface
 * @package Sil\RouteSecurityBundle\Interfaces
 */
interface NamingStrategyInterface
{

    /**
     * Generate a role for specific route
     *
     * @param string $route
     * @return string $role
     */
    public function generateRoleForRoute($route);
}