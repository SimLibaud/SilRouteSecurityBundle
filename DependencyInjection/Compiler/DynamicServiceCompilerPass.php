<?php
/**
 * User: Simon Libaud
 * Date: 19/03/2017
 * Email: simonlibaud@gmail.com
 */

namespace Sil\RouteSecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DynamicServiceCompilerPass
 * @package Sil\RouteSecurityBundle\CompilerPass
 */
class DynamicServiceCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (null !== $naming_strategy_id = $container->getParameter('sil_route_security.naming_strategy')) {
            $container->getDefinition('sil_route_security.access_control')->replaceArgument(1, new Reference($naming_strategy_id));
            $container->getDefinition('sil_route_security.route_security_tools')->replaceArgument(1, new Reference($naming_strategy_id));
        }
    }
}