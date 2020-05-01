<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
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
        $id = EmailField::new('id');
        $email = EmailField::new('email');
        $password = TextField::new('password');
        $roles = CollectionField::new('roles');
        $projects = CollectionField::new('projects');
        $createdAt = DateTimeField::new('createdAt');
        $updatedAt = DateTimeField::new('updatedAt');

        if ($this->isCrudAction('password')) {
            return [$password];
        }

        if (Action::NEW === $pageName) {
            return [$password, $projects, $email, $roles];
        }

        if (Action::EDIT === $pageName) {
            return [
                $email,
                $roles->setFormTypeOption('attr', [
                    'data-autocomplete-url' => $this->generateUrl('user_autocomplete_roles'),
                ]),
                $projects,
            ];
        }

        return [$id, $email, $roles, $projects, $createdAt, $updatedAt];
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
                ->linkToCrudAction(Action::EDIT)
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
