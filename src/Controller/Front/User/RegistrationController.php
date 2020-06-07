<?php

declare(strict_types=1);

namespace App\Controller\Front\User;

use App\Form\User\Handler\UserRegistrationHandler;
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
 * @Route("/user/new", name="front_user_registration", methods={"GET","POST"})
 */
class RegistrationController
{
    /** @required  */
    public Environment $twig;
    /** @required  */
    public RouterInterface $router;
    /** @required  */
    public FormHandlerInterface $handler;

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(): Response
    {
        if ($this->handler->process(UserRegistrationHandler::class)->isValid()) {
            return new RedirectResponse($this->router->generate('app_login'));
        }

        return new Response($this->twig->render('front/user/new.html.twig', [
            'user' => $this->handler->getEntity(),
            'form' => $this->handler->getView(),
        ]));
    }
}
