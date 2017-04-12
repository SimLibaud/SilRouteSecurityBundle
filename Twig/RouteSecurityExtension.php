<?php
/**
 * User: Simon Libaud
 * Date: 19/03/2017
 * Email: simonlibaud@gmail.com
 */

namespace Sil\RouteSecurityBundle\Twig;

use Sil\RouteSecurityBundle\Security\AccessControl;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class RouteSecurityExtension
 * @package Sil\RouteSecurityBundle\Twig
 */
class RouteSecurityExtension extends \Twig_Extension
{

    private $accessControl;

    public function __construct(AccessControl $accessControl)
    {
        $this->accessControl = $accessControl;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('hasUserAccessToRoute', [$this->accessControl, 'hasUserAccessToRoute']),
            new \Twig_SimpleFunction('hasUserAccessToRoutes', [$this->accessControl, 'hasUserAccessToRoutes']),
            new \Twig_SimpleFunction('hasUserAccessAtLeastOneRoute', [$this->accessControl, 'hasUserAccessAtLeastOneRoute'])
        ];
    }

}