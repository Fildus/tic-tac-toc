<?php


namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="home")
 * @Template()
 */
class HomeController
{
    public function __invoke()
    {
    }
}
