<?php

declare(strict_types=1);

namespace App\Controller\Front\Category;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories/autocomplete", name="category_autocomplete", methods={"GET"})
 * @IsGranted("ROLE_USER")
 */
class AutocompleteController
{
    public function __invoke(Request $request, CategoryRepository $categoryRepository): Response
    {
        /** @var Category[] $categories */
        $categories = $categoryRepository->matchByTitle($request->get('title', ''));

        $formatedCategories = [];

        foreach ($categories as $category) {
            $formatedCategories[] = $category->getTitle();
        }

        return new JsonResponse($formatedCategories);
    }
}
