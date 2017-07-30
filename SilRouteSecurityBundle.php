<?php
/**
 * User: Simon Libaud
 * Date: 08/03/2017
 * Email: simonlibaud@gmail.com.
 */
namespace Sil\RouteSecurityBundle;

use Sil\RouteSecurityBundle\DependencyInjection\Compiler\DynamicServiceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SilRouteSecurityBundle.
 */
class SilRouteSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DynamicServiceCompilerPass());
    }
}
