<?php

declare(strict_types=1);

namespace App\Controller\Front\Security;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/logout", name="app_logout", methods={"GET"})
 */
class LogoutController
{
    public function __invoke(): void
    {
    }
}
