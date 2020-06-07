<?php

declare(strict_types=1);

namespace App\Controller\Front\User;

use App\Entity\User;
use App\Form\User\Handler\UserEditEmailHandler;
use App\Form\User\Handler\UserEditProfilHandler;
use App\Form\User\Handler\UserUpdatePasswordHandler;
use App\Infrastructure\FormHandler\FormHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/user/{id}/edit", name="front_user_edit", methods={"GET","POST"})
 */
class EditController
{
    /** @required */
    public Environment $twig;
    /** @required */
    public RouterInterface $router;
    /** @required */
    public FormHandlerInterface $handler;

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(User $user): Response
    {
        if (($editProfileHandler = clone $this->handler)->process(UserEditProfilHandler::class, [], $user)->isValid()) {
            return $this->redirectToUserEdit($user);
        }

        if (($editEmailHandler = clone $this->handler)->process(UserEditEmailHandler::class, [], $user)->isValid()) {
            return $this->redirectToUserEdit($user);
        }

        if (($passwordHandler = clone $this->handler)->process(UserUpdatePasswordHandler::class, [], $user)->isValid()) {
            return $this->redirectToUserEdit($user);
        }

        return new Response($this->twig->render('front/user/edit.html.twig', [
            'user' => $user,
            'formEditProfilAccount' => $editProfileHandler->getView(),
            'formEditEmailAccount' => $editEmailHandler->getView(),
            'formUpdatePassword' => $passwordHandler->getView(),
        ]));
    }

    private function redirectToUserEdit(User $user): Response
    {
        return new RedirectResponse($this->router->generate('front_user_edit', [
            'id' => $user->getId(),
        ]));
    }
}
