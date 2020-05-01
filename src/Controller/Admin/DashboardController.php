<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tic tac toc')
            ->setTranslationDomain('fr');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user);
        $userMenu
            ->setMenuItems([
                MenuItem::linkToLogout('user.sign_out', 'fa-sign-out')->setTranslationDomain('EasyAdminBundle'),
                MenuItem::linktoRoute('homepage', 'fas fa-home', 'home'),
            ]);

        return $userMenu;
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->setPageTitle(Action::INDEX, 'Liste des %entity_label_plural%')
            ->setPageTitle(Action::EDIT, 'Édition d\'un %entity_label_singular% (#%entity_id%)')
            ->setPageTitle(Action::NEW, 'Nouvel %entity_label_singular%')
            ->setPageTitle(Action::DETAIL, '%entity_label_singular% (#%entity_id%)');
    }
}
