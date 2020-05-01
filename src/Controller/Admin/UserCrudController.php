<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Services\EncodePassword;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserCrudController extends AbstractCrudController
{
    use Common;

    private EncodePassword $encodePassword;

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function __construct(EncodePassword $encodePassword)
    {
        $this->encodePassword = $encodePassword;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('utilisateurs')
            ->setEntityLabelInSingular('utilisateur');
    }

    public function configureFields(string $pageName): iterable
    {
        if ($this->isCrudAction('password')) {
            return [
                TextField::new('password'),
            ];
        }

        if (Action::INDEX === $pageName) {
            return [
                IntegerField::new('id'),
                TextField::new('email'),
                CollectionField::new('roles'),
                TextField::new('password')->setMaxLength(30),
            ];
        }

        if (Action::EDIT === $pageName) {
            return [
                TextField::new('email')->setMaxLength(30),
                CollectionField::new('roles')
                    ->setFormTypeOption('attr', [
                        'data-autocomplete-url' => $this->generateUrl('user_autocomplete_roles'),
                    ]),
            ];
        }

        if (Action::NEW === $pageName) {
            return [
                TextField::new('email'),
                CollectionField::new('roles'),
                TextField::new('password')->setMaxLength(30),
            ];
        }

        return [
            TextField::new('email'),
            TextField::new('password'),
            CollectionField::new('roles'),
        ];
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
     * @param object $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword->encode($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * @param object $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword->encode($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
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
