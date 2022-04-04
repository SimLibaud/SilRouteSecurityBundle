<?php
/**
 * User: Simon Libaud
 * Date: 19/03/2017
 * Email: simonlibaud@gmail.com.
 */
namespace Sil\RouteSecurityBundle\Twig;

use Sil\RouteSecurityBundle\Security\AccessControl;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RouteSecurityExtension.
 */
class RouteSecurityExtension extends AbstractExtension
{
    private $accessControl;

    public function __construct(AccessControl $accessControl)
    {
        $this->accessControl = $accessControl;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('hasUserAccessToRoute', [$this->accessControl, 'hasUserAccessToRoute']),
            new TwigFunction('hasUserAccessToRoutes', [$this->accessControl, 'hasUserAccessToRoutes']),
            new TwigFunction('hasUserAccessAtLeastOneRoute', [$this->accessControl, 'hasUserAccessAtLeastOneRoute']),
        ];
    }
}
