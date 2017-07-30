<?php

namespace Sil\RouteSecurityBundle\Tests\WebProfiler;

use PHPUnit\Framework\TestCase;
use Sil\RouteSecurityBundle\Role\RouteToRoleConverter;
use Sil\RouteSecurityBundle\Security\AccessControl;
use Sil\RouteSecurityBundle\WebProfiler\RouteSecurityTools;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RouteSecurityToolsTest extends TestCase
{
    public function testCollect()
    {
        $accessControl = $this->createMock(AccessControl::class);
        $accessControl
            ->method('isEnable')
            ->willReturn(true);
        $accessControl
            ->method('isRouteSecure')
            ->willReturn(true);
        $routeToRoleConverter = $this->createMock(RouteToRoleConverter::class);
        $routeToRoleConverter
            ->method('generateRoleForRoute')
            ->willReturn('ROLE_FOO');

        $routeSecurityTools = new RouteSecurityTools($accessControl, $routeToRoleConverter);
        $request = $this->createMock(Request::class);
        $request
            ->method('get')
            ->with('_route')
            ->willReturn('foo');
        $response = $this->createMock(Response::class);

        $routeSecurityTools->collect($request, $response);

        $this->assertTrue($routeSecurityTools->isAccessControlEnable());
        $this->assertTrue($routeSecurityTools->isRouteSecure());
        $this->assertEquals('ROLE_FOO', $routeSecurityTools->getRoleForRoute());
        $this->assertEquals('sil_route_security.route_security_tools', $routeSecurityTools->getName());
    }
}
