<?php

namespace Sil\RouteSecurityBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Sil\RouteSecurityBundle\Exception\LogicException;
use Sil\RouteSecurityBundle\Interfaces\NamingStrategyInterface;
use Sil\RouteSecurityBundle\Security\AccessControl;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessControlTest extends TestCase
{
    public function testHasUserAccessToRoute()
    {
        $accessControl = $this->createFreshAccessControl();
        $user = $this->mockUser();
        $this->assertTrue($accessControl->hasUserAccessToRoute($user, 'home_page'));
        $this->assertTrue($accessControl->hasUserAccessToRoute($user, 'admin_dashboard'));
        $this->assertFalse($accessControl->hasUserAccessToRoute($user, 'admin_home'));
    }

    public function testHasUserAccessToRoutes()
    {
        $accessControl = $this->createFreshAccessControl();
        $user = $this->mockUser();
        $this->assertTrue($accessControl->hasUserAccessToRoutes($user, ['home_page', 'admin_dashboard']));
        $this->assertFalse($accessControl->hasUserAccessToRoutes($user, ['home_page', 'admin_home']));
    }

    public function testHasUserAccessAtLeastOneRoute()
    {
        $accessControl = $this->createFreshAccessControl();
        $user = $this->mockUser();
        $this->assertTrue($accessControl->hasUserAccessAtLeastOneRoute($user, ['home_page', 'admin_home']));
        $this->assertFalse($accessControl->hasUserAccessAtLeastOneRoute($user, ['admin_home', 'admin_profile']));
    }

    public function testIsRouteSecure()
    {
        $accessControl = $this->createFreshAccessControl();
        $this->assertTrue($accessControl->isRouteSecure('admin_home'));
        $this->assertFalse($accessControl->isRouteSecure('home_page'));
        $this->assertFalse($accessControl->isRouteSecure('api_get_user'));
    }
    
    public function testGetAllSecuredRoutes()
    {
        $accessControl = $this->createFreshAccessControl();
        $all_secured_routes = $accessControl->getAllSecuredRoutes();
        $this->assertContains('admin_home', $all_secured_routes);
        $this->assertContains('admin_dashboard', $all_secured_routes);
        $this->assertContains('admin_profile', $all_secured_routes);
        $this->assertNotContains('api_get_user', $all_secured_routes);
    }

    public function testIsEnable()
    {
        $router = $this->createMock(RouterInterface::class);
        $routeToRoleConverter = $this->createMock(NamingStrategyInterface::class);
        $configuration = [
            'enable_access_control' => true,
            'secured_routes' => [],
            'secured_routes_format' => '',
            'ignored_routes' => [],
            'ignored_routes_format' => '',
        ];
        $accessControl = new AccessControl($router, $routeToRoleConverter, $configuration);
        $this->assertTrue($accessControl->isEnable());
        $configuration['enable_access_control'] = false;
        $accessControl = new AccessControl($router, $routeToRoleConverter, $configuration);
        $this->assertFalse($accessControl->isEnable());
    }

    protected function createFreshAccessControl()
    {
        $router = $this->createMock(RouterInterface::class);
        $routeCollection = $this->createMock(RouteCollection::class);
        $routeCollection
            ->method('all')
            ->willReturn([
                'admin_home' => null,
                'admin_dashboard' => null,
                'admin_profile' => null,
                'home_page' => null,
                'create_account' => null,
                'api_get_info' => null,
                'api_set_info' => null
            ]);
        $router
            ->method('getRouteCollection')
            ->willReturn($routeCollection);
        $routeToRoleConverter = $this->createMock(NamingStrategyInterface::class);
        $routeToRoleConverter
            ->method('generateRoleForRoute')
            ->will($this->returnCallback(function ($route) {
                return 'ROLE_'.strtoupper($route);
            }));
        $configuration = [
            'enable_access_control' => true,
            'secured_routes' => ['admin_home'],
            'secured_routes_format' => '/^admin_/',
            'ignored_routes' => ['home_page'],
            'ignored_routes_format' => '/^api_/',
        ];

        return new AccessControl($router, $routeToRoleConverter, $configuration);
    }

    protected function mockUser()
    {
        $user = $this->createMock(UserInterface::class);
        $user
            ->method('getRoles')
            ->willReturn(['ROLE_ADMIN_DASHBOARD']);

        return $user;
    }
}
