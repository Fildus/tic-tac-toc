<?php

declare(strict_types=1);

namespace App\Tests\Controller\Back;

use App\Controller\Admin\ProjectCrudController;
use App\Entity\Project;
use App\Entity\User;
use App\Tests\FixturesTrait;
use App\Utils\StringUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Admin\ProjectCrudController
 *
 * @group ProjectControllerTest
 */
class ProjectControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Admin\ProjectCrudController::index
     */
    public function test admin project index response is successful(): void
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
    public function test admin project new response is successful create new(): void
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

        $projectTitle = StringUtils::stringToLength('new Project title', 15);
        $values['Project']['title'] = $projectTitle;
        $values['Project']['content'] = StringUtils::stringToLength('new Project content', 75);
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
    public function test admin project edit response is successful(): void
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
    }

    /**
     * @covers \App\Controller\Admin\ProjectCrudController::edit
     */
    public function test admin project edit response is successful update content(): void
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
        $content = StringUtils::stringToLength('content updated', 75);
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
    public function test admin project edit response is successful set user(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy([]);

        /** @var Project $project */
        $project = self::$em->getRepository(Project::class)->findOneBy([]);

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
        $values['Project']['user'] = $user->getId();
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
    public function test admin project delete response is successful delete project(): void
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
