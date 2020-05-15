<?php

declare(strict_types=1);

namespace App\Controller\Front\Project;

use App\Repository\ProjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/projects/mes-projects", name="project_user_index", methods={"GET"})
 * @IsGranted("ROLE_USER")
 */
class UserIndexController
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
    public function __invoke(ProjectRepository $projectRepository): Response
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return new Response($this->twig->render('front/project/user_index.html.twig', [
            'projects' => $projectRepository->findBy(['user' => $user]),
        ]));
    }
}
