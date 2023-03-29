<?php
declare(strict_types=1);

namespace Sil\RouteSecurityBundle\Controller;

use Sil\RouteSecurityBundle\Exception\LogicException;
use Sil\RouteSecurityBundle\Security\AccessControl;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;

class ExportJsSecuredRoutesController
{
    /** @var AccessControl */
    private $accessControl;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var Environment */
    private $twig;

    /** @var string */
    private $cacheDir;

    /**
     * @param AccessControl $accessControl
     * @param TokenStorageInterface $tokenStorage
     * @param Environment $twig
     * @param string $cacheDir
     */
    public function __construct(AccessControl $accessControl, TokenStorageInterface $tokenStorage, Environment $twig, string $cacheDir)
    {
        $this->accessControl = $accessControl;
        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
        $this->cacheDir = $cacheDir;
    }

    /**
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function exportAction()
    {
        if (null === $this->tokenStorage->getToken()) {
            throw new LogicException('Unable to retrive the current user. The token storage does not contain security token.');
        }

        if (false === $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
            throw new LogicException(sprintf('The security token must containt an User object that implements %s', UserInterface::class));
        }

        $user = $this->tokenStorage->getToken()->getUser();

        $cacheKey = md5($user->getUserIdentifier().json_encode($user->getRoles()));

        $cache = new FilesystemAdapter('sil_route_security_bundle', 0, $this->cacheDir);

        $securedRoutesWithUserPermission = $cache->get($cacheKey, function (ItemInterface $item) use ($user){
            $item->expiresAfter(3600);

            $securedRoutesWithUserPermission = [];
            foreach ($this->accessControl->getAllSecuredRoutes() as $route) {
                $securedRoutesWithUserPermission[$route] = $this->accessControl->hasUserAccessToRoute($user, $route);
            }

            return $securedRoutesWithUserPermission;
        });

        return new Response($this->twig->render(
            '@SilRouteSecurity/secured_routes.js.twig',
            ['securedRoutes' => $securedRoutesWithUserPermission]
        ), 200, ['Content-Type' => 'application/javascript']);
    }
}
