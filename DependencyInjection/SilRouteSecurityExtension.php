<?php
/**
 * User: Simon Libaud
 * Date: 12/03/2017
 * Email: simonlibaud@gmail.com.
 */
namespace Sil\RouteSecurityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class SilRouteSecurityExtension.
 */
class SilRouteSecurityExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Bundle configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Load service definition
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // Set container parameters
        $container->setParameter('sil_route_security.enable_access_control', $config['enable_access_control']);
        $container->setParameter('sil_route_security.secured_routes', $config['secured_routes']);
        $container->setParameter('sil_route_security.secured_routes_format', $config['secured_routes_format']);
        $container->setParameter('sil_route_security.ignored_routes', $config['ignored_routes']);
        $container->setParameter('sil_route_security.ignored_routes_format', $config['ignored_routes_format']);
        $container->setParameter('sil_route_security.naming_strategy', $config['naming_strategy']);
    }
}
