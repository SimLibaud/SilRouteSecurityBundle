<?php
/**
 * User: Simon Libaud
 * Date: 12/03/2017
 * Email: simonlibaud@gmail.com.
 */
namespace Sil\RouteSecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $tree = new TreeBuilder('sil_route_security');
        // Keep compatibility with symfony/config < 4.2
        if (false === method_exists($tree, 'getRootNode')) {
            $root = $tree->root('sil_route_security');
        } else {
            $root = $tree->getRootNode();
        }

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
