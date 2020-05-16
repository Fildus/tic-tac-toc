<?php

declare(strict_types=1);

namespace App\Controller\Front\User;

use App\Entity\User;
use App\Form\User\Handler\UserDeleteHandler;
use App\Infrastructure\DeleteHandler\DeleteHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/user/{id}", name="front_user_delete", methods={"DELETE"})
 * @IsGranted("ROLE_USER")
 */
class DeleteController
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(DeleteHandlerInterface $h, User $user): RedirectResponse
    {
        if ($h->process($user, UserDeleteHandler::class)->isValid()) {
            return new RedirectResponse($this->router->generate('app_logout'));
        }

        return new RedirectResponse($this->router->generate('home'));
    }
}
