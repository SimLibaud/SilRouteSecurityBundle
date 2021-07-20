<?php

namespace Sil\RouteSecurityBundle\Tests\Listener;

use PHPUnit\Framework\TestCase;
use Sil\RouteSecurityBundle\Event\AccessDeniedToRouteEvent;
use Sil\RouteSecurityBundle\Exception\LogicException;
use Sil\RouteSecurityBundle\Listener\AccessControlListener;
use Sil\RouteSecurityBundle\Security\AccessControl;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessControlListenerTest extends TestCase
{
    public function testWhenAccessControlIsDisabled()
    {
        $accessControl = $this->createMock(AccessControl::class);
        $accessControl
            ->method('isEnable')
            ->willReturn(false);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $event = $this->mockRequestEvent();

        $accessControlListener = new AccessControlListener($accessControl, $tokenStorage, $eventDispatcher);
        $accessControlListener->onKernelRequest($event);

        $this->assertNull($accessControlListener->onKernelRequest($event));
    }

    public function testWithNonSecureRoute()
    {
        $accessControl = $this->createMock(AccessControl::class);
        $accessControl
            ->method('isEnable')
            ->willReturn(true);
        $accessControl
            ->method('isRouteSecure')
            ->with('non_secure_route')
            ->willReturn(false);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $event = $this->mockRequestEvent('non_secure_route');

        $accessControlListener = new AccessControlListener($accessControl, $tokenStorage, $eventDispatcher);
        $accessControlListener->onKernelRequest($event);

        $this->assertNull($accessControlListener->onKernelRequest($event));
    }

    public function testWithEmptyTokenStorage()
    {
        $accessControl = $this->createMock(AccessControl::class);
        $accessControl
            ->method('isEnable')
            ->willReturn(true);
        $accessControl
            ->method('isRouteSecure')
            ->with('secure_route')
            ->willReturn(true);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->method('getToken')
            ->willReturn(null);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $event = $this->mockRequestEvent('secure_route');

        $accessControlListener = new AccessControlListener($accessControl, $tokenStorage, $eventDispatcher);
        $this->expectException(LogicException::class);
        $accessControlListener->onKernelRequest($event);
    }

    public function testWithInvalidTokenStorage()
    {
        $accessControl = $this->createMock(AccessControl::class);
        $accessControl
            ->method('isEnable')
            ->willReturn(true);
        $accessControl
            ->method('isRouteSecure')
            ->with('secure_route')
            ->willReturn(true);
        $token = $this->createMock(TokenInterface::class);
        $token
            ->method('getUser')
            ->willReturn('invalid user type');
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->method('getToken')
            ->willReturn($token);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $event = $this->mockRequestEvent('secure_route');

        $accessControlListener = new AccessControlListener($accessControl, $tokenStorage, $eventDispatcher);
        $this->expectException(LogicException::class);
        $accessControlListener->onKernelRequest($event);
    }

    public function testWithCorrectAccessForUser()
    {
        $accessControl = $this->createMock(AccessControl::class);
        $accessControl
            ->method('isEnable')
            ->willReturn(true);
        $accessControl
            ->method('isRouteSecure')
            ->with('secure_route')
            ->willReturn(true);
        $accessControl
            ->method('hasUserAccessToRoute')
            ->willReturn(true);
        $tokenStorage = $this->mockTokenStorage();
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $event = $this->mockRequestEvent('secure_route');

        $accessControlListener = new AccessControlListener($accessControl, $tokenStorage, $eventDispatcher);
        $this->assertNull($accessControlListener->onKernelRequest($event));
    }

    public function testWithIncorrectAccessForUser()
    {
        $accessControl = $this->createMock(AccessControl::class);
        $accessControl
            ->method('isEnable')
            ->willReturn(true);
        $accessControl
            ->method('isRouteSecure')
            ->with('secure_route')
            ->willReturn(true);
        $accessControl
            ->method('hasUserAccessToRoute')
            ->willReturn(false);
        $tokenStorage = $this->mockTokenStorage();
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $event = $this->mockRequestEvent('secure_route');

        $accessControlListener = new AccessControlListener($accessControl, $tokenStorage, $eventDispatcher);
        $this->expectException(AccessDeniedException::class);
        $this->assertNull($accessControlListener->onKernelRequest($event));
    }

    public function testWithIncorrectAccessForUserAndCustomResponse()
    {
        $accessControl = $this->createMock(AccessControl::class);
        $accessControl
            ->method('isEnable')
            ->willReturn(true);
        $accessControl
            ->method('isRouteSecure')
            ->with('secure_route')
            ->willReturn(true);
        $accessControl
            ->method('hasUserAccessToRoute')
            ->willReturn(false);
        $tokenStorage = $this->mockTokenStorage();
        $event = $this->mockRequestEvent('secure_route');
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $response = new Response('Custom Response');
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(AccessDeniedToRouteEvent::class), AccessDeniedToRouteEvent::ON_ACCESS_DENIED_TO_ROUTE)
            ->will($this->returnCallback(function ($access_denied_event, $name) use ($response) {
                $access_denied_event->setResponse($response);
                return $access_denied_event;
            }));

        $accessControlListener = new AccessControlListener($accessControl, $tokenStorage, $eventDispatcher);
        $this->assertInstanceOf(RequestEvent::class, $accessControlListener->onKernelRequest($event));
    }

    protected function mockRequestEvent($route = null)
    {
        $event = $this->createMock(RequestEvent::class);
        $request = $this->createMock(Request::class);
        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag
            ->method('get')
            ->with('_route')
            ->willReturn($route);

        $request->attributes = $parameterBag;
        $event->method('getRequest')->willReturn($request);

        return $event;
    }

    protected function mockTokenStorage($roles = [])
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $user = $this->createMock(UserInterface::class);
        $user
            ->method('getRoles')
            ->willReturn($roles);
        $token = $this->createMock(TokenInterface::class);
        $token
            ->method('getUser')
            ->willReturn($user);
        $tokenStorage
            ->method('getToken')
            ->willReturn($token);

        return $tokenStorage;
    }
}
