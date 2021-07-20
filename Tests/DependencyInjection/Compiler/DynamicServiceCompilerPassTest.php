<?php

namespace Sil\RouteSecurityBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Sil\RouteSecurityBundle\DependencyInjection\Compiler\DynamicServiceCompilerPass;
use Sil\RouteSecurityBundle\Interfaces\NamingStrategyInterface;
use Sil\RouteSecurityBundle\Role\RouteToRoleConverter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DynamicServiceCompilerPassTest extends TestCase
{
    public function testDefaultNamingStrategy()
    {
        $container = new ContainerBuilder();
        $container->setParameter('sil_route_security.naming_strategy', null);
        $container->register('sil_route_security.route_to_role_converter', RouteToRoleConverter::class);
        $container->register('router', null);
        $container
            ->register('sil_route_security.access_control')
            ->addArgument(new Reference('router'))
            ->addArgument(new Reference('sil_route_security.route_to_role_converter'))
        ;
        $container
            ->register('sil_route_security.route_security_tools', null)
            ->addArgument(new Reference('sil_route_security.access_control'))
            ->addArgument(new Reference('sil_route_security.route_to_role_converter'))
        ;

        $compiler = new DynamicServiceCompilerPass();
        $compiler->process($container);

        $this->assertEquals('sil_route_security.route_to_role_converter', $container->getDefinition('sil_route_security.access_control')->getArgument(1)->__toString());
        $this->assertEquals('sil_route_security.route_to_role_converter', $container->getDefinition('sil_route_security.route_security_tools')->getArgument(1)->__toString());
    }

    public function testOverridesNamingStrategy()
    {
        $container = new ContainerBuilder();
        $container->setParameter('sil_route_security.naming_strategy', 'my_own_naming_strategy');
        $container->register('my_own_naming_strategy', NamingStrategyInterface::class);
        $container->register('sil_route_security.route_to_role_converter', RouteToRoleConverter::class);
        $container->register('router', null);
        $container
            ->register('sil_route_security.access_control')
            ->addArgument(new Reference('router'))
            ->addArgument(new Reference('sil_route_security.route_to_role_converter'))
        ;
        $container
            ->register('sil_route_security.route_security_tools', null)
            ->addArgument(new Reference('sil_route_security.access_control'))
            ->addArgument(new Reference('sil_route_security.route_to_role_converter'))
        ;

        $compiler = new DynamicServiceCompilerPass();
        $compiler->process($container);

        $this->assertEquals('my_own_naming_strategy', $container->getDefinition('sil_route_security.access_control')->getArgument(1)->__toString());
        $this->assertEquals('my_own_naming_strategy', $container->getDefinition('sil_route_security.route_security_tools')->getArgument(1)->__toString());
    }
}
