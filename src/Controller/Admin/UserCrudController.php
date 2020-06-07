<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Utils\StringUtils;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserCrudController extends AbstractCrudController
{
    use Common;

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('utilisateurs')
            ->setEntityLabelInSingular('utilisateur')
            ->setDateFormat('dd-MM-y');
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id', 'Identifiant');
        $email = EmailField::new('email', 'Email');
        $password = TextField::new('password', 'Password');
        $roles = CollectionField::new('roles', 'Rôles');
        $projects = AssociationField::new('projects', 'Projects')->setFormTypeOptions([
            'by_reference' => false,
            'expanded' => true,
            'multiple' => true,
        ]);
        $categories = AssociationField::new('categories', 'Categories')->setFormTypeOptions([
            'by_reference' => false,
            'expanded' => true,
            'multiple' => true,
        ]);
        $createdAt = DateTimeField::new('createdAt');
        $updatedAt = DateTimeField::new('updatedAt');

        if ($this->isCrudAction('password')) {
            return compact('password');
        }

        if (Action::NEW === $pageName) {
            return compact('email', 'password', 'roles', 'projects', 'categories');
        }

        if (Action::EDIT === $pageName) {
            $roles->setFormTypeOption('attr', [
                'data-autocomplete-url' => $this->generateUrl('user_autocomplete_roles'),
            ]);

            return compact('email', 'roles', 'projects', 'categories');
        }

        return compact('id', 'email', 'roles', 'projects', 'categories', 'createdAt', 'updatedAt');
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->add(Action::INDEX,
                Action::new('edit password')->linkToCrudAction('password')
            )
            ->add(Action::EDIT, Action::new('Modifier le password')
                ->linkToCrudAction('password')
                ->setCssClass('btn btn-info')
                ->displayIf(fn () => !$this->isRouteName('password'))
            )->add(Action::EDIT, Action::new('Édition de l\'utilisateur')
                ->linkToRoute('admin', function (User $user) {
                    return [
                        'crudAction' => Action::EDIT,
                        'crudId' => StringUtils::getControlllerId(UserCrudController::class),
                        'entityId' => $user->getId(),
                    ];
                })
                ->setCssClass('btn btn-info')
                ->displayIf(fn () => $this->isRouteName('password'))
            );

        return $actions;
    }

    public function password(AdminContext $context): Response
    {
        return $this->redirectToCrud($context, Action::EDIT);
    }

    /**
     * @Route("/admin/user_autocomplete_roles/{role?}", name="user_autocomplete_roles", methods={"GET"})
     */
    public function autocompleteRoles(?string $role): JsonResponse
    {
        $profiles = [User::ROLE_USER, User::ROLE_ADMIN];
        $profilesFiltered = [];

        if (null !== $role) {
            foreach ($profiles as $k => $v) {
                if (preg_match_all('#'.strtolower($role).'#', strtolower($v))) {
                    $profilesFiltered[] = $v;
                }
            }
        }

        $response = !empty($profilesFiltered) ? $profilesFiltered : $profiles;

        return $this->json($response);
    }
}
