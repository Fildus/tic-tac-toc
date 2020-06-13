<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\DataTransformerInterface;

class CategoryToCollectionTransfomer implements DataTransformerInterface
{
    /** @required */
    public CategoryRepository $categoryRepository;

    public function transform($value)
    {
        return implode(',', array_map(function (Category $categoryEntity) {
            return $categoryEntity->getTitle();
        }, $value->toArray()));
    }

    public function reverseTransform($value)
    {
        if (null !== $value) {
            $categories = array_map(function (string $categoryTitle) {
                return $this->categoryRepository->findOneBy(['title' => $categoryTitle]) ?? null;
            }, explode(',', $value));

            $categories = array_filter($categories, function ($category) {
                return null !== $category;
            });
        }

        return $categories ?? [];
    }
}
