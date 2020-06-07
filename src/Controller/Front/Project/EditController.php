<?php

declare(strict_types=1);

namespace App\Controller\Front\Project;

use App\Entity\Project;
use App\Form\Project\Handler\ProjectEditHandler;
use App\Infrastructure\FormHandler\FormHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/projects/{id}/edit", name="project_edit", methods={"GET","POST"})
 * @IsGranted("ROLE_USER")
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
    public function __invoke(Project $project, FormHandlerInterface $h): Response
    {
        if ($h->process(ProjectEditHandler::class, [], $project)->isValid()) {
            return new RedirectResponse($this->router->generate('project_edit', [
                'id' => $project->getId(),
            ]));
        }

        return new Response($this->twig->render('front/project/edit.html.twig', [
            'project' => $project,
            'form' => $h->getView(),
        ]));
    }
}
