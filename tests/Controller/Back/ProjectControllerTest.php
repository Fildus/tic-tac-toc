<?php

namespace App\Tests\Controller\Back;

use App\Controller\Admin\ProjectCrudController;
use App\Entity\Project;
use App\Entity\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Admin\ProjectCrudController
 * @group ProjectControllerTest
 */
class ProjectControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Admin\ProjectCrudController::index
     */
    public function test_adminProjectIndex_responseIsSuccessful(): void
    {
        self::setUpClient(User::ROLE_ADMIN);
        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'index',
                'crudController' => ProjectCrudController::class,
            ])
        );

        self::assertResponseIsSuccessful();
    }

    /**
     * @covers \App\Controller\Admin\ProjectCrudController::new
     */
    public function test_adminProjectNew_responseIsSuccessful_createNew(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'new',
                'crudController' => ProjectCrudController::class,
            ])
        );

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Créer')->form();
        $values = $form->getPhpValues();

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        $projectTitle = 'new Project title';
        $values['Project']['title'] = $projectTitle;
        $values['Project']['content'] = 'new Project content';
        $values['Project']['user'] = $user->getId();
        $form->setValues($values);
        self::$client->submit($form);

        self::$client->followRedirect();

        self::assertResponseIsSuccessful();
        static::assertNotEmpty(self::$em->getRepository(Project::class)->findBy(['title' => $projectTitle]));
    }

    /**
     * @covers \App\Controller\Admin\ProjectCrudController::edit
     */
    public function test_adminProjectEdit_responseIsSuccessful(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var Project $project */
        $project = self::$em->getRepository(Project::class)->findOneBy([], []);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => ProjectCrudController::class,
                'entityId' => $project->getId(),
            ])
        );

        self::assertResponseIsSuccessful();
        static::assertStringContainsString((string) $project->getTitle(), (string) self::$client->getResponse()->getContent());
        static::assertStringContainsString((string) $project->getContent(), (string) self::$client->getResponse()->getContent());
    }

    /**
     * @covers \App\Controller\Admin\ProjectCrudController::edit
     */
    public function test_adminProjectEdit_responseIsSuccessful_updateContent(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var Project $project */
        $project = self::$em->getRepository(Project::class)->findOneBy([], []);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => ProjectCrudController::class,
                'entityId' => $project->getId(),
            ])
        );

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();
        $content = 'content updated';
        $values['Project']['content'] = $content;
        $form->setValues($values);
        self::$client->submit($form);

        $crawler = self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertStringContainsString('Liste des projects', $crawler->text());
        static::assertNotEmpty(self::$em->getRepository(Project::class)->findBy(['content' => $content]));
    }

    /**
     * @covers \App\Controller\Admin\ProjectCrudController::edit
     */
    public function test_adminProjectEdit_responseIsSuccessful_setUser(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var Project $project */
        $project = self::$em->getRepository(Project::class)->findOneBy(['user' => null]);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => ProjectCrudController::class,
                'entityId' => $project->getId(),
            ])
        );
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();
        $values['Project']['user'] = 1;
        self::$client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertStringContainsString('Liste des projects', (string) self::$client->getResponse()->getContent());
        $project = self::$em->getRepository(Project::class)->findOneBy(['id' => $project->getId()]);
        static::assertTrue(null !== $project->getUser());
    }

    /**
     * @covers \App\Controller\Admin\ProjectCrudController::delete
     */
    public function test_adminProjectDelete_responseIsSuccessful_deleteProject(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var Project $project */
        $project = self::$em->getRepository(Project::class)->findOneBy([]);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'index',
                'crudController' => ProjectCrudController::class,
            ])
        );

        $form = $crawler->filter('#main form')->form();
        $values = $form->getValues();
        self::$client->request(
            $form->getMethod(),
            str_replace('__entityId_placeholder__', (string) $project->getId(), (string) $form->getUri()),
            $values
        );
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertNull(self::$em->getRepository(Project::class)->findOneBy(['id' => $project->getId()]));
    }
}
