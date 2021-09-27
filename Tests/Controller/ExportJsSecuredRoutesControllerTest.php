<?php
declare(strict_types=1);

namespace Sil\RouteSecurityBundle\Tests\Controller;


use Sil\RouteSecurityBundle\Tests\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class ExportJsSecuredRoutesControllerTest extends WebTestCase
{
    public function testWithUnauthenticatedUser()
    {
        $client  = static::createClient();

        $crawler  = $client->request('GET', '/sil-route-security/export-js-secured-routes.js');
        $response = $client->getResponse();

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testWithUserHasAccessToRoute()
    {
        $client  = static::createClient();

        $user = $this->createMock(UserInterface::class);
        $user->method('getRoles')->willReturn(['ROLE_APP_SECURED_ROUTE_TEST']);
        $client->loginUser($user);

        $crawler  = $client->request('GET', '/sil-route-security/export-js-secured-routes.js');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(<<<JAVASCRIPT
// List of secured routes and associated permissions for current authenticated user
SilRouteSecurity.addSecuredRoutes('app_secured_route_test', true);

JAVASCRIPT
       , $response->getContent());
    }

    public function testWithUserHasNotAccessToRoute()
    {
        $client  = static::createClient();

        $user = $this->createMock(UserInterface::class);
        $user->method('getRoles')->willReturn([]);
        $client->loginUser($user);

        $crawler  = $client->request('GET', '/sil-route-security/export-js-secured-routes.js');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(<<<JAVASCRIPT
// List of secured routes and associated permissions for current authenticated user
SilRouteSecurity.addSecuredRoutes('app_secured_route_test', false);

JAVASCRIPT
            , $response->getContent());
    }
}
