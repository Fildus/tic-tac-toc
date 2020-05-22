<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('projects')
            ->setEntityLabelInSingular('project')
            ->setDateFormat('dd-MM-y');
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id', 'Identifiant');
        $title = TextField::new('title', 'Titre');
        $content = TextareaField::new('content', 'Contenu')->setMaxLength(30);
        $user = AssociationField::new('user', 'Utilisateur');
        $categories = AssociationField::new('categories', 'categories')->setFormTypeOptions([
            'by_reference' => false,
            'expanded' => true,
            'multiple' => true,
        ]);
        $createdAt = DateTimeField::new('createdAt', 'Date de création');
        $updatedAt = DateTimeField::new('updatedAt', 'Date de modification');

        if (Action::NEW === $pageName) {
            return compact('title', 'content', 'user', 'categories');
        }

        if (Action::EDIT === $pageName) {
            return compact('title', 'content', 'user', 'categories');
        }

        if (Action::DETAIL === $pageName) {
            return compact('id', 'title', 'content', 'user', 'categories', 'createdAt', 'updatedAt');
        }

        return compact('title', 'content', 'user', 'categories', 'createdAt', 'updatedAt');
    }
}
