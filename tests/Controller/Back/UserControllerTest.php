<?php

declare(strict_types=1);

namespace App\Tests\Controller\Back;

use App\Controller\Admin\UserCrudController;
use App\Entity\Category;
use App\Entity\Project;
use App\Entity\User;
use App\Tests\FixturesTrait;
use App\Utils\StringUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\Admin\UserCrudController
 *
 * @group UserBackControllerTest
 */
class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Admin\UserCrudController::index
     */
    public function test admin user index response is successful(): void
    {
        self::setUpClient(User::ROLE_ADMIN);
        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'index',
                'crudId' => StringUtils::getControlllerId(UserCrudController::class),
            ])
        );
        self::assertResponseIsSuccessful();
    }

    /**
     * @covers \App\Controller\Admin\UserCrudController::new
     */
    public function test admin user new response is successful create new(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'new',
                'crudId' => StringUtils::getControlllerId(UserCrudController::class),
            ])
        );
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Créer')->form();
        $values = $form->getPhpValues();
        $email = 'someoneNew@test.test';
        $values['User']['email'] = $email;
        $values['User']['password'] = 'test';
        $values['User']['roles'][0] = User::ROLE_ADMIN;
        $form->setValues($values);
        self::$client->submit($form);

        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        static::assertNotEmpty(self::$em->getRepository(User::class)->findBy(['email' => $email]));
    }

    /**
     * @covers \App\Controller\Admin\UserCrudController::edit
     */
    public function test admin user edit response is successful(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudId' => StringUtils::getControlllerId(UserCrudController::class),
                'entityId' => $user->getId(),
            ])
        );

        self::assertResponseIsSuccessful();
        static::assertStringContainsString('Modifier le password', (string) self::$client->getResponse()->getContent());

        static::assertStringContainsString((string) $user->getEmail(), (string) self::$client->getResponse()->getContent());
        foreach ($user->getRoles() as $role) {
            static::assertStringContainsString($role, (string) self::$client->getResponse()->getContent());
        }
    }

    /**
     * @covers \App\Controller\Admin\UserCrudController::edit
     */
    public function test admin user edit response is successful update email(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudId' => StringUtils::getControlllerId(UserCrudController::class),
                'entityId' => $user->getId(),
            ])
        );

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();
        $email = 'someoneNew2@test.test';
        $values['User']['email'] = $email;
        $form->setValues($values);
        self::$client->submit($form);

        $crawler = self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertStringContainsString('Liste des utilisateurs', $crawler->text());
        static::assertNotEmpty(self::$em->getRepository(User::class)->findBy(['email' => $email]));
    }

    /**
     * @covers \App\Controller\Admin\UserCrudController::edit
     */
    public function test admin user edit response is successful add projects(): void
    {
        self::setUpClient(User::ROLE_ADMIN);
        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudId' => StringUtils::getControlllerId(UserCrudController::class),
                'entityId' => $user->getId(),
            ])
        );

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();
        $values['User']['projects'] = [];

        $projects = self::$em->getRepository(Project::class)->findBy([], [], 3);
        $projectsIdAsArray = [];
        foreach ($projects as $project) {
            $projectsIdAsArray[$project->getId() - 1] = $project->getId();
        }

        $values['User']['projects'] = $projectsIdAsArray;

        self::$client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        $crawler = self::$client->followRedirect();

        self::assertResponseIsSuccessful();
        static::assertStringContainsString('Liste des utilisateurs', $crawler->text());

        $projectsAfter = self::$em->getRepository(Project::class)->findBy([], [], 3);
        $projectsIdsAfter = [];
        foreach ($projectsAfter as $project) {
            $projectsIdsAfter[] = $project->getId();
        }

        static::assertSame($projectsIdAsArray, $projectsIdsAfter);
    }

    /**
     * @covers \App\Controller\Admin\CategoryCrudController::edit
     */
    public function test admin user edit response is successful add and remove category(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy([]);
        /** @var Category $category */
        $category = self::$em->getRepository(Category::class)->findOneBy([]);

        /**
         * Add a Category and check.
         */
        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudId' => StringUtils::getControlllerId(UserCrudController::class),
                'entityId' => $category->getId(),
            ])
        );
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();
        $values['User']['categories'][] = $category->getId();
        $form->setValues($values);
        self::$client->submit($form);
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        /** @var User $newUser */
        $newUser = self::$em->getRepository(User::class)->findOneBy(['id' => $user->getId()]);
        /** @var Category $newCategory */
        $newCategory = self::$em->getRepository(Category::class)->findOneBy(['id' => $category->getId()]);
        static::assertTrue($newUser->getCategories()->contains($newCategory));

        /**
         * Remove a Category and check.
         */
        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudId' => StringUtils::getControlllerId(UserCrudController::class),
                'entityId' => $category->getId(),
            ])
        );
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();
        foreach ($values['User']['categories'] as $k => $v) {
            $values['User']['categories'][$k] = false;
        }
        $form->setValues($values);
        self::$client->submit($form);
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        /** @var Category $newCategory */
        $newCategory = self::$em->getRepository(Category::class)->findOneBy(['id' => $category->getId()]);
        /** @var User $newUser */
        $newUser = self::$em->getRepository(User::class)->findOneBy(['id' => $user->getId()]);
        static::assertFalse($newUser->getCategories()->contains($newCategory));
    }

    /**
     * @covers \App\Controller\Admin\UserCrudController::edit
     */
    public function test admin user edit password response is successful edit password(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudId' => StringUtils::getControlllerId(UserCrudController::class),
                'route_name' => 'password',
                'entityId' => $user->getId(),
            ])
        );
        self::assertResponseIsSuccessful();
        static::assertStringContainsString('Édition de l\'utilisateur', $crawler->text());
    }

    /**
     * @covers \App\Controller\Admin\UserCrudController::edit
     */
    public function test admin user edit response is successful update password(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        $email = 'user@user.com';
        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => $email]);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudId' => StringUtils::getControlllerId(UserCrudController::class),
                'entityId' => $user->getId(),
                'route_name' => 'password',
            ])
        );
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();
        $values['User']['password'] = 'new password';
        $form->setValues($values);
        self::$client->submit($form);

        $crawler = self::$client->followRedirect();

        self::assertResponseIsSuccessful();
        static::assertStringContainsString('Liste des utilisateurs', $crawler->text());

        /** @var User $user */
        $updatedUser = self::$em->getRepository(User::class)->findOneBy(['email' => $email]);
        static::assertNotEmpty($updatedUser);
        static::assertNotEquals($user->getPassword(), $updatedUser->getPassword());
    }

    /**
     * @covers \App\Controller\Admin\UserCrudController::delete
     */
    public function test admin user delete response is successful delete user(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        $email = 'user@user.com';
        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => $email]);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'index',
                'crudId' => StringUtils::getControlllerId(UserCrudController::class),
            ])
        );

        $form = $crawler->filter('#main #delete-form')->form();
        $values = $form->getValues();
        self::$client->request(
            $form->getMethod(),
            str_replace('__entityId_placeholder__', (string) $user->getId(), $form->getUri()),
            $values
        );

        self::$client->followRedirect();
        static::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
        static::assertNull(self::$em->getRepository(User::class)->findOneBy(['email' => $email]));
    }
}
