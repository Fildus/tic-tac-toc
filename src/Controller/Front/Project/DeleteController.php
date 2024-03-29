<?php

declare(strict_types=1);

namespace App\Controller\Front\Project;

use App\Entity\Project;
use App\Form\Project\Handler\ProjectDeleteHandler;
use App\Infrastructure\DeleteHandler\DeleteHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/project/{id}", name="project_delete", methods={"DELETE"})
 * @IsGranted("ROLE_USER")
 */
class DeleteController
{
    /** @required */
    public RouterInterface $router;
    /** @required */
    public DeleteHandlerInterface $handler;

    public function __invoke(Project $project): RedirectResponse
    {
        if ($this->handler->process($project, ProjectDeleteHandler::class)->isValid()) {
            return new RedirectResponse($this->router->generate('project_user_index'));
        }

        return new RedirectResponse($this->router->generate('home'));
    }
}
