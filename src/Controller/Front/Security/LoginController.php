<?php

declare(strict_types=1);

namespace App\Controller\Front\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/login", name="app_login", methods={"GET", "POST"})
 */
class LoginController
{
    private Environment $twig;
    private TokenStorageInterface $tokenStorage;
    private RouterInterface $router;

    public function __construct(Environment $twig, TokenStorageInterface $tokenStorage, RouterInterface $router)
    {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(AuthenticationUtils $authenticationUtils): Response
    {
        $token = $this->tokenStorage->getToken();

        if (is_object($user = $token->getUser())) {
            return new RedirectResponse($this->router->generate('home'));
        }

        return new Response($this->twig->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]));
    }
}
