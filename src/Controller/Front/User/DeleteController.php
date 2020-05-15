<?php

declare(strict_types=1);

namespace App\Controller\Front\User;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/user/{id}", name="front_user_delete", methods={"DELETE"})
 */
class DeleteController
{
    private ManagerRegistry $managerRegistry;
    private RouterInterface $router;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        ManagerRegistry $managerRegistry,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request, FlashBagInterface $flashBag, User $user): RedirectResponse
    {
        $token = new CsrfToken('delete'.$user->getId(), $request->request->get('_token'));
        if ($this->csrfTokenManager->isTokenValid($token)) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $this->tokenStorage->setToken(null);

            $flashBag->add('notice', 'Votre compte a bien été supprimé');

            return new RedirectResponse($this->router->generate('app_logout'));
        }

        return new RedirectResponse($this->router->generate('home'));
    }
}
