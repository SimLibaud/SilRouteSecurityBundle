<?php

namespace Sil\RouteSecurityBundle\Tests;

use PHPUnit\Framework\TestCase;
use Sil\RouteSecurityBundle\DependencyInjection\Compiler\DynamicServiceCompilerPass;
use Sil\RouteSecurityBundle\SilRouteSecurityBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SilRouteSecurityBundleTest extends TestCase
{
    public function testBuild()
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $containerBuilder
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(DynamicServiceCompilerPass::class))
        ;
        $silRouteSecurityBundle = new SilRouteSecurityBundle();
        $this->assertNull($silRouteSecurityBundle->build($containerBuilder));
    }
}
