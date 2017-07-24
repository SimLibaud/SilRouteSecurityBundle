<?php
/**
 * User: Simon Libaud
 * Date: 20/03/2017
 * Email: simonlibaud@gmail.com
 */

namespace Sil\RouteSecurityBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccessDeniedToRouteEvent
 * @package Sil\RouteSecurityBundle\Event
 */
class AccessDeniedToRouteEvent extends Event
{

    const ON_ACCESS_DENIED_TO_ROUTE = 'sil_route_security.event.access_denied_to_route';

    private $user;
    private $request;
    private $response;

    /**
     * AccessDeniedToRouteEvent constructor.
     * @param UserInterface $user
     * @param Request $request
     */
    public function __construct(UserInterface $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
        $this->response = null;
    }

    /**
     * @return UserInterface $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Request $request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return null|Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }
}