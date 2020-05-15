<?php

declare(strict_types=1);

namespace App\Tests\Controller\Back;

use App\Controller\Admin\UserCrudController;
use App\Entity\Project;
use App\Entity\User;
use App\Tests\FixturesTrait;
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
    public function test_adminUserIndex_responseIsSuccessful(): void
    {
        self::setUpClient(User::ROLE_ADMIN);
        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'index',
                'crudController' => UserCrudController::class,
            ])
        );
        self::assertResponseIsSuccessful();
    }

    /**
     * @covers \App\Controller\Admin\UserCrudController::new
     */
    public function test_adminUserNew_responseIsSuccessful_createNew(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'new',
                'crudController' => UserCrudController::class,
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
    public function test_adminUserEdit_responseIsSuccessful(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => UserCrudController::class,
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
    public function test_adminUserEdit_responseIsSuccessful_updateEmail(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => UserCrudController::class,
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
    public function test_adminUserEdit_responseIsSuccessful_addProjects(): void
    {
        self::setUpClient(User::ROLE_ADMIN);
        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => UserCrudController::class,
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
     * @covers \App\Controller\Admin\UserCrudController::edit
     */
    public function test_adminUserEditPassword_responseIsSuccessful_editPassword(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => UserCrudController::class,
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
    public function test_adminUserEdit_responseIsSuccessful_updatePassword(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        $email = 'user@user.com';
        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => $email]);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => UserCrudController::class,
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
    public function test_adminUserDelete_responseIsSuccessful_deleteUser(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        $email = 'user@user.com';
        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => $email]);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'index',
                'crudController' => UserCrudController::class,
            ])
        );

        $form = $crawler->filter('#main form')->form();
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
