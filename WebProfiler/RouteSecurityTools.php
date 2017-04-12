<?php
/**
 * User: Simon Libaud
 * Date: 20/03/2017
 * Email: simonlibaud@gmail.com
 */

namespace Sil\RouteSecurityBundle\WebProfiler;

use Sil\RouteSecurityBundle\Interfaces\NamingStrategyInterface;
use Sil\RouteSecurityBundle\Security\AccessControl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class RouteSecurityTools
 * @package Sil\RouteSecurityBundle\WebProfiler
 */
class RouteSecurityTools extends DataCollector
{

    private $accessControl;
    private $routeToRoleConverter;
    private $token;

    public function __construct(AccessControl $accessControl, NamingStrategyInterface $routeToRoleConverter, TokenStorageInterface $token)
    {
        $this->accessControl = $accessControl;
        $this->routeToRoleConverter = $routeToRoleConverter;
        $this->token = $token;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['is_access_control_enable'] = $this->accessControl->isEnable();
        $route = $request->get('_route');
        $this->data['is_route_secure'] = $this->accessControl->isRouteSecure($route);
        $this->data['role_for_route'] = $this->routeToRoleConverter->generateRoleForRoute($route);
    }

    public function isAccessControlEnable()
    {
        return $this->data['is_access_control_enable'];
    }

    public function isRouteSecure()
    {
        return $this->data['is_route_secure'];
    }

    public function getRoleForRoute()
    {
        return $this->data['role_for_route'];
    }

    public function getName()
    {
        return 'sil_route_security.route_security_tools';
    }
}