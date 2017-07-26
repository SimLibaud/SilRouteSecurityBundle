<?php

namespace Sil\RouteSecurityBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sil\RouteSecurityBundle\DependencyInjection\SilRouteSecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class SilRouteSecurityExtensionTest extends TestCase
{
    public function testConfigurationWithDefaultValues()
    {
        $extension = new SilRouteSecurityExtension();
        $container = new ContainerBuilder();
        $config = [];
        $extension->load($config, $container);

        $this->assertFalse($container->getParameter('sil_route_security.enable_access_control'));
        $this->assertNull($container->getParameter('sil_route_security.secured_routes_format'));
        $this->assertNull($container->getParameter('sil_route_security.ignored_routes_format'));
        $this->assertEmpty($container->getParameter('sil_route_security.secured_routes'));
        $this->assertEmpty($container->getParameter('sil_route_security.ignored_routes'));
        $this->assertNull($container->getParameter('sil_route_security.naming_strategy'));
    }

    public function testConfigurationWithOverridesValues()
    {
        $config = $this->getFullConfiguration();
        $extension = new SilRouteSecurityExtension();
        $container = new ContainerBuilder();
        $extension->load($config, $container);

        $this->assertTrue($container->getParameter('sil_route_security.enable_access_control'));
        $this->assertEquals('/regex_secured_routes/', $container->getParameter('sil_route_security.secured_routes_format'));
        $this->assertEquals('/regex_ignored_routes/', $container->getParameter('sil_route_security.ignored_routes_format'));
        $this->assertEquals(['route_admin'], $container->getParameter('sil_route_security.secured_routes'));
        $this->assertEquals(['route_foo', 'route_bar'], $container->getParameter('sil_route_security.ignored_routes'));
        $this->assertEquals('my_own_naming_strategy_service_id', $container->getParameter('sil_route_security.naming_strategy'));
    }

    protected function getFullConfiguration()
    {
        $yaml = <<<EOF
sil_route_security:
    enable_access_control: true
    secured_routes_format: '/regex_secured_routes/'
    ignored_routes_format: '/regex_ignored_routes/'
    secured_routes: [route_admin]
    ignored_routes: [route_foo, route_bar]
    naming_strategy: my_own_naming_strategy_service_id
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }
}
