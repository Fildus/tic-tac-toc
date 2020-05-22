<?php

declare(strict_types=1);

namespace App\Tests\Controller\Back;

use App\Controller\Admin\CategoryCrudController;
use App\Entity\Category;
use App\Entity\Project;
use App\Entity\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Admin\CategoryCrudController
 *
 * @group CategoryControllerTest
 */
class CategoryControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Admin\CategoryCrudController::index
     */
    public function test admin category index response is successful(): void
    {
        self::setUpClient(User::ROLE_ADMIN);
        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'index',
                'crudController' => CategoryCrudController::class,
            ])
        );

        self::assertResponseIsSuccessful();
    }

    /**
     * @covers \App\Controller\Admin\CategoryCrudController::new
     */
    public function test admin category new response is successful create new(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'new',
                'crudController' => CategoryCrudController::class,
            ])
        );

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Créer')->form();
        $values = $form->getPhpValues();

        /** @var Category $parent */
        $parent = self::$em->getRepository(Category::class)->findOneBy([]);

        $newTitle = 'Category new title';
        $values['Category']['title'] = $newTitle;
        $values['Category']['parent'] = $parent->getId();
        $form->setValues($values);
        self::$client->submit($form);

        self::$client->followRedirect();

        self::assertResponseIsSuccessful();
        static::assertNotEmpty(self::$em->getRepository(Category::class)->findOneBy(['title' => $newTitle]));
    }

    /**
     * @covers \App\Controller\Admin\CategoryCrudController::new
     */
    public function test admin category edit response is successful add and remove project(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var Category $category */
        $category = self::$em->getRepository(Category::class)->findOneBy([]);

        /** @var Project $project */
        $project = self::$em->getRepository(Project::class)->findOneBy([]);

        /**
         * Add a Project and check.
         */
        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => CategoryCrudController::class,
                'entityId' => $category->getId(),
            ])
        );
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();
        $values['Category']['projects'][] = $project->getId();
        $form->setValues($values);
        self::$client->submit($form);
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        /** @var Category $newCategory */
        $newCategory = self::$em->getRepository(Category::class)->findOneBy(['id' => $category->getId()]);
        /** @var Project $newProject */
        $newProject = self::$em->getRepository(Project::class)->findOneBy(['id' => $project->getId()]);
        static::assertTrue($newCategory->getProjects()->contains($newProject));

        /**
         * Remove a Project and check.
         */
        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'edit',
                'crudController' => CategoryCrudController::class,
                'entityId' => $category->getId(),
            ])
        );
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $values = $form->getPhpValues();
        foreach ($values['Category']['projects'] as $k => $v) {
            $values['Category']['projects'][$k] = false;
        }
        $form->setValues($values);
        self::$client->submit($form);
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        /** @var Project $newProject */
        $newProject = self::$em->getRepository(Project::class)->findOneBy(['id' => $project->getId()]);
        /** @var Category $newCategory */
        $newCategory = self::$em->getRepository(Category::class)->findOneBy(['id' => $category->getId()]);
        static::assertFalse($newCategory->getProjects()->contains($newProject));
    }

    /**
     * @covers \App\Controller\Admin\CategoryCrudController::delete
     */
    public function test admin category delete response is successful delete category(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        /** @var Category $category */
        $category = self::$em->getRepository(Category::class)->findOneBy([]);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', [
                'crudAction' => 'index',
                'crudController' => CategoryCrudController::class,
            ])
        );

        $form = $crawler->filter('#main form')->form();
        $values = $form->getValues();
        self::$client->request(
            $form->getMethod(),
            str_replace('__entityId_placeholder__', (string) $category->getId(), (string) $form->getUri()),
            $values
        );
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertNull(self::$em->getRepository(Category::class)->findOneBy(['id' => $category->getId()]));
    }
}
