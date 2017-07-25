<?php

namespace Sil\RouteSecurityBundle\Tests\Event;

use PHPUnit\Framework\TestCase;
use Sil\RouteSecurityBundle\Event\AccessDeniedToRouteEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessDeniedToRouteEventTest extends TestCase
{
    private function getFreshEvent()
    {
        $user = $this->createMock(UserInterface::class);
        $request = $this->createMock(Request::class);

        return new AccessDeniedToRouteEvent($user, $request);
    }

    public function testGetUser()
    {
        $event = $this->getFreshEvent();
        $this->assertInstanceOf(UserInterface::class, $event->getUser());
    }

    public function testGetRequest()
    {
        $event = $this->getFreshEvent();
        $this->assertInstanceOf(Request::class, $event->getRequest());
    }

    public function testGetResponseAsNull()
    {
        $event = $this->getFreshEvent();
        $this->assertNull($event->getResponse());
    }

    public function testGetResponse()
    {
        $event = $this->getFreshEvent();
        $response = $this->createMock(Response::class);
        $event->setResponse($response);

        $this->assertEquals($response, $event->getResponse());
    }

    public function testHasResponse()
    {
        $event = $this->getFreshEvent();
        $this->assertFalse($event->hasResponse());

        $response = $this->createMock(Response::class);
        $event->setResponse($response);
        $this->assertTrue($event->hasResponse());
    }
}
