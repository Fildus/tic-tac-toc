<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Utils\StringUtils;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class CategoryCrudController extends AbstractCrudController
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $parentId = $this->get('request_stack') instanceof RequestStack ?
            (int) $this->get('request_stack')->getCurrentRequest()->get('__parentId', 0) :
            null;

        /** @var Category $parent */
        $parent = $this->categoryRepository->find($parentId);

        $crud
            ->setEntityLabelInPlural('categories')
            ->setEntityLabelInSingular('categorie')
            ->setDateFormat('dd-MM-y');
        if (null !== $parent) {
            $crud->setHelp(Action::INDEX, '<a href="'.$this->generateUrl('admin', [
                    'crudAction' => Action::INDEX,
                    'crudId' => StringUtils::getControlllerId(CategoryCrudController::class),
                    '__parentId' => $parent->getParent() ? $parent->getParent()->getId() : null,
                ]).'">⇐ parent</a>');
            $crud->setHelp(Action::EDIT, '<a href="'.$this->generateUrl('admin', [
                    'crudAction' => Action::EDIT,
                    'crudId' => StringUtils::getControlllerId(CategoryCrudController::class),
                    '__parentId' => $parent->getParent() ? $parent->getParent()->getId() : null,
                ]).'">⇐ parent</a>');
        }

        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id', 'Identifiant');
        $title = TextField::new('title', 'Titre');
        $parent = AssociationField::new('parent', 'Catégorie parente');
        $children = AssociationField::new('children', 'Enfants')->setFormTypeOptions([
            'by_reference' => false,
            'expanded' => true,
            'multiple' => true,
        ]);
        $users = AssociationField::new('users', 'Users')->setFormTypeOptions([
            'by_reference' => false,
            'expanded' => true,
            'multiple' => true,
        ]);
        $projects = AssociationField::new('projects', 'Projets')->setFormTypeOptions([
            'by_reference' => false,
            'expanded' => true,
            'multiple' => true,
        ]);
        $createdAt = DateTimeField::new('createdAt', 'Date de création');
        $updatedAt = DateTimeField::new('updatedAt', 'Date de modification');

        if (Action::NEW === $pageName) {
            return compact('title', 'parent', 'children', 'users', 'projects');
        }

        if (Action::EDIT === $pageName) {
            return compact('title', 'parent', 'children', 'users', 'projects');
        }

        if (Action::DETAIL === $pageName) {
            return compact('id', 'title', 'parent', 'children', 'users', 'projects', 'createdAt', 'updatedAt');
        }

        return compact('id', 'title', 'parent', 'children', 'users', 'projects', 'createdAt', 'updatedAt');
    }

    public function configureActions(Actions $actions): Actions
    {
        $children = Action::new('children', false, 'fas fa-code-branch')
            ->linkToRoute('admin', function (Category $entity) {
                return [
                    'crudAction' => Action::INDEX,
                    'crudId' => StringUtils::getControlllerId(CategoryCrudController::class),
                    '__parentId' => $entity->getId(),
                ];
            })
            ->displayIf(function (Category $category) {
                return !$category->getChildren()->isEmpty();
            });

        return parent::configureActions($actions)
            ->add(Action::INDEX, $children);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $parent = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $parentId = $searchDto->getRequest()->get('__parentId', null);
        if (null !== $parentId) {
            $parent->where('entity.parent = '.$searchDto->getRequest()->get('__parentId', null));
        } else {
            $parent->where('entity.parent is null');
        }

        return $parent;
    }
}
