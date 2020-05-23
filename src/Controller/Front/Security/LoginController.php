<?php

declare(strict_types=1);

namespace App\Controller\Front\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
    private FlashBagInterface $flashBag;

    public function __construct(
        Environment $twig,
        TokenStorageInterface $tokenStorage,
        RouterInterface $router,
        FlashBagInterface $flashBag
    ) {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->flashBag = $flashBag;
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
            if ($user instanceof User) {
                $this->flashBag->add('success', [
                    'header' => '<strong>'.$user->getUsername().'</strong>',
                    'body' => 'vous êtes maintenant connecté',
                ]);
            }

            return new RedirectResponse($this->router->generate('home'));
        }

        return new Response($this->twig->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]));
    }
}
