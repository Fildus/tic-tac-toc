<?php

declare(strict_types=1);

namespace App\Controller\Front\Navbar;

use App\Cache\CacheKeys;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;

class RenderNavbar
{
    /** @required */
    public UrlGeneratorInterface $generator;
    /** @required */
    public Environment $twig;
    /** @required */
    public AuthorizationCheckerInterface $authorizationChecker;
    /** @required */
    public TokenStorageInterface $tokenStorage;
    /** @required */
    public CacheInterface $cache;

    public function __invoke(): Response
    {
        return $this->cache->get(CacheKeys::RENDER_NAVBAR, function (ItemInterface $item) {
            $item->expiresAfter(CacheKeys::RENDER_NAVBAR_TIME);

            return new Response($this->twig->render('navbar.html.twig', [
                'projects' => $this->getProjects(),
                'connection' => $this->getConnection(),
            ]));
        });
    }

    private function getProjects(): array
    {
        $projects['main']['href'] = $this->generator->generate('project_index');
        $projects['main']['html'] = 'Liste des projects';

        $projects['subLinks'] = [
            [
                'href' => $this->generator->generate('project_new'),
                'html' => 'Créer un project',
            ],
            [
                'href' => $this->generator->generate('project_user_index'),
                'html' => 'Mes projects',
            ],
        ];

        return $projects;
    }

    private function getConnection(): array
    {
        $connection['main']['href'] = $this->generator->generate('app_login');
        $connection['main']['html'] = 'Connection';

        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $connection['subLinks'][] = [
                    'href' => $this->generator->generate('admin'),
                    'html' => 'Admin',
                ];
            }
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();
            $connection['subLinks'][] = [
                'href' => $this->generator->generate('front_user_edit', ['id' => $user->getId()]),
                'html' => 'Mon compte',
            ];
            $connection['subLinks'][] = [
                'href' => $this->generator->generate('app_logout'),
                'html' => 'logout',
            ];
        } else {
            $connection['subLinks'][] = [
                'href' => $this->generator->generate('app_login'),
                'html' => 'login',
            ];
            $connection['subLinks'][] = [
                'href' => $this->generator->generate('front_user_registration'),
                'html' => 'Créer un compte',
            ];
        }

        return $connection;
    }
}
