<?php

/**
 * User: Simon Libaud
 * Date: 19/03/2017
 * Email: simonlibaud@gmail.com.
 */
namespace Sil\RouteSecurityBundle\Listener;

use Sil\RouteSecurityBundle\Event\AccessDeniedToRouteEvent;
use Sil\RouteSecurityBundle\Exception\LogicException;
use Sil\RouteSecurityBundle\Security\AccessControl;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AccessControlListener.
 */
class AccessControlListener
{
    private $accessControl;
    private $tokenStorage;
    private $eventDispatcher;

    public function __construct(AccessControl $accessControl, TokenStorageInterface $tokenStorage, EventDispatcherInterface $eventDispatcher)
    {
        $this->accessControl = $accessControl;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param RequestEvent $event
     * @return RequestEvent|void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $route = $event->getRequest()->attributes->get('_route');

        if (null === $route || false === $this->accessControl->isEnable() || false === $this->accessControl->isRouteSecure($route)) {
            return;
        }

        if (null === $this->tokenStorage->getToken()) {
            throw new LogicException('Unable to retrive the current user. The token storage does not contain security token.');
        }

        if (false === $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
            throw new LogicException(sprintf('The security token must containt an User object that implements %s', UserInterface::class));
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if (false === $this->accessControl->hasUserAccessToRoute($user, $route)) {
            $access_denied_event = new AccessDeniedToRouteEvent($user, $event->getRequest());
            $this->eventDispatcher->dispatch($access_denied_event, AccessDeniedToRouteEvent::ON_ACCESS_DENIED_TO_ROUTE);

            if (true === $access_denied_event->hasResponse()) {
                $event->setResponse($access_denied_event->getResponse());

                return $event;
            }

            throw new AccessDeniedException();
        }
    }
}
