<?php

declare(strict_types=1);

namespace App\Controller\Front\Project;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/project/{id}", name="project_delete", methods={"DELETE"})
 * @IsGranted("ROLE_USER")
 */
class DeleteController
{
    private ManagerRegistry $managerRegistry;
    private RouterInterface $router;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        ManagerRegistry $managerRegistry,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request, FlashBagInterface $flashBag, Project $project): RedirectResponse
    {
        $token = new CsrfToken('delete'.$project->getId(), $request->request->get('_token'));
        if ($this->csrfTokenManager->isTokenValid($token)) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->remove($project);
            $entityManager->flush();

            $flashBag->add('notice', 'Le project a bien été supprimé');

            return new RedirectResponse($this->router->generate('project_user_index'));
        }

        return new RedirectResponse($this->router->generate('home'));
    }
}
