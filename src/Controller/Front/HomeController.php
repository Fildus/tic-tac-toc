<?php

declare(strict_types=1);

namespace App\Controller\Front;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/", name="home", methods={"GET"})
 */
class HomeController
{
    /** @required */
    public Environment $twig;

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(): Response
    {
        return new Response($this->twig->render('front/home.html.twig'));
    }
}
