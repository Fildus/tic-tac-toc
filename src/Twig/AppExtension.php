<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private RequestStack $requestStack;
    private UrlGeneratorInterface $generator;
    private AuthorizationCheckerInterface $authorizationChecker;

    private TokenStorageInterface $tokenStorage;

    public function __construct(
        RequestStack $requestStack,
        UrlGeneratorInterface $generator,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage
    ) {
        $this->requestStack = $requestStack;
        $this->generator = $generator;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('appNavbarConfig', [$this, 'appNavbarConfig']),
        ];
    }

    public function appNavbarConfig(): array
    {
        $config['home']['href'] = $this->generator->generate('home');
        $config['home']['html'] = 'Tic tac toc';

        $config['dropdown']['projects']['main']['href'] = $this->generator->generate('project_index');
        $config['dropdown']['projects']['main']['html'] = 'Liste des projects';

        $config['dropdown']['projects']['subLinks'][] = [
            'href' => $this->generator->generate('project_new'),
            'html' => 'Créer un project',
        ];
        $config['dropdown']['projects']['subLinks'][] = [
            'href' => $this->generator->generate('project_userIndex'),
            'html' => 'Mes projects',
        ];

        $config['dropdown']['connection']['main']['href'] = $this->generator->generate('app_login');
        $config['dropdown']['connection']['main']['html'] = 'Connection';
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $config['dropdown']['connection']['subLinks'][] = [
                    'href' => $this->generator->generate('admin'),
                    'html' => 'Admin',
                ];
            }
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();
            $config['dropdown']['connection']['subLinks'][] = [
                'href' => $this->generator->generate('front_user_edit', ['id' => $user->getId()]),
                'html' => 'Gérer mon compte',
            ];
            $config['dropdown']['connection']['subLinks'][] = [
                'href' => $this->generator->generate('app_logout'),
                'html' => 'logout',
            ];
        } else {
            $config['dropdown']['connection']['subLinks'][] = [
                'href' => $this->generator->generate('app_login'),
                'html' => 'login',
            ];
            $config['dropdown']['connection']['subLinks'][] = [
                'href' => $this->generator->generate('front_user_new'),
                'html' => 'Créer un compte',
            ];
        }

        return $config;
    }
}
