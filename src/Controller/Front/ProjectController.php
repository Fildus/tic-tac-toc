<?php

namespace App\Controller\Front;

use App\Entity\Project;
use App\Form\Project\Handler\ProjectEditHandler;
use App\Form\Project\Handler\ProjectNewHandler;
use App\Form\Project\Type\ProjectType;
use App\Infrastructure\FormHandler\FormHandlerInterface;
use App\Repository\ProjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects", name="project_")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('front/project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/mes-projects", name="userIndex", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function userIndex(ProjectRepository $projectRepository): Response
    {
        return $this->render('front/project/userIndex.html.twig', [
            'projects' => $projectRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    /**
     * @Route("/nouveau-project", name="new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(FormHandlerInterface $handler): Response
    {
        $handler->process(ProjectNewHandler::class, ['user' => $this->getUser()]);

        if ($handler->isValid()) {
            return $this->redirectToRoute('project_index');
        }

        return $this->render('front/project/new.html.twig', [
            'project' => $handler->getEntity(),
            'form' => $handler->getView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function show(Project $project): Response
    {
        return $this->render('front/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Project $project, FormHandlerInterface $handler): Response
    {
        $handler->process(ProjectEditHandler::class, [], $project);

        if ($handler->isValid()) {
            return $this->redirectToRoute('project_index');
        }

        return $this->render('front/project/edit.html.twig', [
            'project' => $handler->getEntity(),
            'form' => $handler->getView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }
}
