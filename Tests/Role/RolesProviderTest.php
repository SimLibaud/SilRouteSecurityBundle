<?php

namespace Sil\RouteSecurityBundle\Tests\Role;

use PHPUnit\Framework\TestCase;
use Sil\RouteSecurityBundle\Role\RolesProvider;
use Sil\RouteSecurityBundle\Role\RouteToRoleConverter;

class RolesProviderTest extends TestCase
{
    public function testGetRoles()
    {
        $secured_routes = ['route_foo', 'route_bar'];
        $routeToRoleConverter = $this->createMock(RouteToRoleConverter::class);
        $routeToRoleConverter
            ->expects($this->exactly(count($secured_routes)))
            ->method('generateRoleForRoute')
            ->will($this->returnCallback(function ($route) {
                return 'ROLE_'.strtoupper($route);
            }));
        $rolesProvider = new RolesProvider($routeToRoleConverter, $secured_routes);
        $this->assertEquals(['ROLE_ROUTE_FOO', 'ROLE_ROUTE_BAR'], $rolesProvider->getRoles());
    }
}
