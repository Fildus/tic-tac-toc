<?php

namespace App\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="home", methods={"GET"})
 * @Template()
 */
class HomeController
{
    public function __invoke(): void
    {
    }
}