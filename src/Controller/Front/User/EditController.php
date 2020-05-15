<?php

declare(strict_types=1);

namespace App\Controller\Front\User;

use App\Entity\User;
use App\Form\User\Handler\UserEditAccountHandler;
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
    private Environment $twig;
    private RouterInterface $router;

    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->twig = $twig;
        $this->router = $router;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(FormHandlerInterface $h, User $user): Response
    {
        if (($editHandler = clone $h)->process(UserEditAccountHandler::class, [], $user)->isValid()) {
            return new RedirectResponse($this->router->generate('front_user_edit', [
                'id' => $user->getId(),
            ]));
        }

        if (($passwordHandler = clone $h)->process(UserUpdatePasswordHandler::class, [], $user)->isValid()) {
            return new RedirectResponse($this->router->generate('front_user_edit', [
                'id' => $user->getId(),
            ]));
        }

        return new Response($this->twig->render('front/user/edit.html.twig', [
            'user' => $user,
            'formEditAccount' => $editHandler->getView(),
            'formUpdatePassword' => $passwordHandler->getView(),
        ]));
    }
}
