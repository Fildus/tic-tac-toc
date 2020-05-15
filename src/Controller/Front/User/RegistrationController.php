<?php

declare(strict_types=1);

namespace App\Controller\Front\User;

use App\Form\User\Handler\UserRegistrationHandler;
use App\Infrastructure\FormHandler\FormHandlerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/user/new", name="front_user_new", methods={"GET","POST"})
 */
class RegistrationController
{
    private Environment $twig;
    private FormFactoryInterface $formFactory;
    private ManagerRegistry $managerRegistry;
    private RouterInterface $router;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        ManagerRegistry $managerRegistry,
        RouterInterface $router
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->managerRegistry = $managerRegistry;
        $this->router = $router;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(FormHandlerInterface $h): Response
    {
        if ($h->process(UserRegistrationHandler::class)->isValid()) {
            return new RedirectResponse($this->router->generate('app_login'));
        }

        return new Response($this->twig->render('front/user/new.html.twig', [
            'user' => $h->getEntity(),
            'form' => $h->getView(),
        ]));
    }
}
