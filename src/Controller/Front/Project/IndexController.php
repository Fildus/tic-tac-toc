<?php

declare(strict_types=1);

namespace App\Controller\Front\Project;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/projects", name="project_index", methods={"GET"})
 */
class IndexController
{
    /** @required */
    public Environment $twig;

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(ProjectRepository $projectRepository): Response
    {
        return new Response($this->twig->render('front/project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]));
    }
}
