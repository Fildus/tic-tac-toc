<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryRepository extends NestedTreeRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Category::class));
    }

    public function matchByTitle(string $title): QueryBuilder
    {
        return $this->createQueryBuilder('category')
            ->where("category.title LIKE '%{$title}%'")
            ->getQuery()
            ->getResult();
    }
}
