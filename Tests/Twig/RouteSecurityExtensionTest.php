<?php

namespace Sil\RouteSecurityBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Sil\RouteSecurityBundle\Security\AccessControl;
use Sil\RouteSecurityBundle\Twig\RouteSecurityExtension;

class RouteSecurityExtensionTest extends TestCase
{
    public function testGetFunctions()
    {
        $accessControl = $this->createMock(AccessControl::class);
        $routeSecurityExtension = new RouteSecurityExtension($accessControl);
        $twig_functions = $routeSecurityExtension->getFunctions();
        $this->assertEquals(3, count($twig_functions));

        $functions =  ['hasUserAccessToRoute', 'hasUserAccessToRoutes', 'hasUserAccessAtLeastOneRoute'];
        foreach ($twig_functions as $index => $twig_function) {
            $this->assertTrue(in_array($twig_function->getName(), $functions));
        }
    }
}
