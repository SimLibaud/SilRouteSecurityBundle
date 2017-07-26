<?php

namespace Sil\RouteSecurityBundle\Tests\Role;

use PHPUnit\Framework\TestCase;
use Sil\RouteSecurityBundle\Role\RouteToRoleConverter;

class RouteToRoleConverterTest extends TestCase
{
    public function testGenerateRoleForRoute()
    {
        $converter = new RouteToRoleConverter();

        $this->assertEquals('ROLE_ADMIN_AREA', $converter->generateRoleForRoute('admin_area'));
    }
}