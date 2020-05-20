<?php

declare(strict_types=1);

namespace App\Controller\Front\Project;

use App\Entity\Project;
use App\Form\Project\Handler\ProjectNewHandler;
use App\Infrastructure\FormHandler\FormHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/projects/nouveau-project", name="project_new", methods={"GET","POST"})
 * @IsGranted("ROLE_USER")
 */
class NewController
{
    private Environment $twig;
    private RouterInterface $router;
    private TokenStorageInterface $tokenStorage;

    public function __construct(Environment $twig, RouterInterface $router, TokenStorageInterface $tokenStorage)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(FormHandlerInterface $h, Request $request): Response
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($h->process(ProjectNewHandler::class, ['user' => $user], $project = new Project())->isValid()) {
            return new RedirectResponse($this->router->generate('project_user_index', [
                'id' => $project->getId(),
            ]));
        }

        return new Response($this->twig->render('front/project/new.html.twig', [
            'form' => $h->getView(),
        ]));
    }
}
