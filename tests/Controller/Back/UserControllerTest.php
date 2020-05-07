<?php

namespace App\Tests\Controller\Back;

use App\Controller\Admin\UserCrudController;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Tests\ClientTest;
use App\Tests\Database;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group UserControllerTest
 */
class UserControllerTest extends WebTestCase
{
    public function test_adminUserIndex_responseIsSuccessful(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin', [
                'crudAction' => 'index',
                'crudController' => UserCrudController::class,
            ])
        );

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function test_adminUserNew_responseIsSuccessful_createNew(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin', [
                'crudAction' => 'new',
                'crudController' => UserCrudController::class,
            ])
        );

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Créer')->form();
        $values = $form->getPhpValues();

        $email = 'someoneNew@test.test';

        $values['User']['email'] = $email;
        $values['User']['password'] = 'test';
        $values['User']['roles'][0] = User::ROLE_ADMIN;
        $form->setValues($values);
        $client->submit($form);

        $client->followRedirect();

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        static::assertNotEmpty($client->getContainer()->get('doctrine')->getRepository(User::class)->findBy(['email' => $email]));
    }

    public function test_adminEdit_responseIsSuccessful(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);

        $userDb = $client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
        /** @var User $data */
        $data = $userDb->findOneBy(['email' => 'user@user.com']);

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => UserCrudController::class,
                'entityId' => $data->getId(),
            ])
        );

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        static::assertStringContainsString('Modifier le password', (string) $client->getResponse()->getContent());

        static::assertStringContainsString((string) $data->getEmail(), (string) $client->getResponse()->getContent());
        foreach ($data->getRoles() as $role) {
            static::assertStringContainsString($role, (string) $client->getResponse()->getContent());
        }
    }

    public function test_adminEdit_responseIsSuccessful_updateEmail(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);

        $userDb = $client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
        /** @var User $data */
        $data = $userDb->findOneBy(['email' => 'user@user.com']);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => UserCrudController::class,
                'entityId' => $data->getId(),
            ])
        );

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();

        $email = 'someoneNew2@test.test';

        $values['User']['email'] = $email;
        $form->setValues($values);
        $client->submit($form);

        $crawler = $client->followRedirect();

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        static::assertStringContainsString('Liste des utilisateurs', $crawler->text());
        static::assertNotEmpty($client->getContainer()->get('doctrine')->getRepository(User::class)->findBy(['email' => $email]));
    }

    public function test_adminEdit_responseIsSuccessful_addProjects(): void
    {
        Database::reload();
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);

        /** @var User $user */
        $user = $client
            ->getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@user.com']);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => UserCrudController::class,
                'entityId' => $user->getId(),
            ])
        );

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();
        $values['User']['projects'] = [];

        /** @var ProjectRepository $projectRepository */
        $projectRepository = $client
            ->getContainer()
            ->get('doctrine')
            ->getRepository(Project::class);

        $projects = $projectRepository->findBy([], [], 3);

        $projectsIdAsArray = [];
        foreach ($projects as $project) {
            $projectsIdAsArray[$project->getId() - 1] = $project->getId();
        }

        $values['User']['projects'] = $projectsIdAsArray;

        $client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        $crawler = $client->followRedirect();
        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        static::assertStringContainsString('Liste des utilisateurs', $crawler->text());

        $projectRepository = $client
            ->getContainer()
            ->get('doctrine')
            ->getRepository(Project::class);

        $projectsAfter = $projectRepository->findBy([], [], 3);

        $projectsIdsAfter = [];
        foreach ($projectsAfter as $project) {
            $projectsIdsAfter[] = $project->getId();
        }

        static::assertSame($projectsIdAsArray, $projectsIdsAfter);
    }

    public function test_adminEditPassword_responseIsSuccessful(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);

        $userDb = $client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
        /** @var User $data */
        $data = $userDb->findOneBy(['email' => 'user@user.com']);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => UserCrudController::class,
                'route_name' => 'password',
                'entityId' => $data->getId(),
            ])
        );

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        static::assertStringContainsString('Édition de l\'utilisateur', $crawler->text());
    }

    public function test_adminEdit_responseIsSuccessful_updatePassword(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);

        $userDb = $client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
        $email = 'user@user.com';
        /** @var User $data */
        $data = $userDb->findOneBy(['email' => $email]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => UserCrudController::class,
                'entityId' => $data->getId(),
                'route_name' => 'password',
            ])
        );

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();

        $values['User']['password'] = 'new password';

        $form->setValues($values);
        $client->submit($form);

        $crawler = $client->followRedirect();

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        static::assertStringContainsString('Liste des utilisateurs', $crawler->text());

        /** @var User $user */
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => $email]);
        static::assertNotEmpty($user);
        static::assertNotEquals($user->getPassword(), $data->getPassword());
    }

    public function test_adminDelete_responseIsSuccessful_deleteUser(): void
    {
        Database::reload();
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);

        $repository = $client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);

        $email = 'user@user.com';

        /** @var User $user */
        $user = $repository->findOneBy(['email' => $email]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin', [
                'crudAction' => 'index',
                'crudController' => UserCrudController::class,
                'page' => 1,
            ])
        );

        $form = $crawler->filter('#main form')->form();
        $values = $form->getValues();

        $client->request(
            $form->getMethod(),
            str_replace('__entityId_placeholder__', (string) $user->getId(), (string) $form->getUri()),
            $values
        );

        $client->followRedirect();

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        static::assertNull($repository->findOneBy(['email' => $email]));
    }
}
