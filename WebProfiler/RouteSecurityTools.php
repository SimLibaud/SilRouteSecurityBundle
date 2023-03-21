<?php
/**
 * User: Simon Libaud
 * Date: 20/03/2017
 * Email: simonlibaud@gmail.com.
 */
namespace Sil\RouteSecurityBundle\WebProfiler;

use Sil\RouteSecurityBundle\Interfaces\NamingStrategyInterface;
use Sil\RouteSecurityBundle\Security\AccessControl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class RouteSecurityTools.
 */
class RouteSecurityTools extends DataCollector
{
    private $accessControl;
    private $routeToRoleConverter;

    public function __construct(AccessControl $accessControl, NamingStrategyInterface $routeToRoleConverter)
    {
        $this->accessControl = $accessControl;
        $this->routeToRoleConverter = $routeToRoleConverter;
    }

    /**
     * @param Request         $request
     * @param Response        $response
     * @param \Throwable|null $exception
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
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

    public function getName(): string
    {
        return 'sil_route_security.route_security_tools';
    }
    
    public function reset(): void
    {
    }
}
