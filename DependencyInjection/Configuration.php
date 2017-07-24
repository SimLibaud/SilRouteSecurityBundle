<?php
/**
 * User: Simon Libaud
 * Date: 12/03/2017
 * Email: simonlibaud@gmail.com
 */

namespace Sil\RouteSecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Sil\RouteSecurityBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tree = new TreeBuilder();
        $root = $tree->root('sil_route_security');

        $root
            ->children()
                ->booleanNode('enable_access_control')->defaultFalse()->end()
                ->scalarNode('secured_routes_format')->defaultNull()->end()
                ->scalarNode('ignored_routes_format')->defaultNull()->end()
                ->arrayNode('ignored_routes')->prototype('scalar')->end()->end()
                ->arrayNode('secured_routes')->prototype('scalar')->end()->end()
                ->scalarNode('naming_strategy')->defaultNull()->end()
        ;

        return $tree;
    }
}