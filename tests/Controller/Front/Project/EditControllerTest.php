<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front\Project;

use App\Entity\Category;
use App\Entity\Project;
use App\Entity\User;
use App\Tests\FixturesTrait;
use App\Utils\StringUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Front\Project\EditController
 *
 * @group Project\EditControllerTest
 */
class EditControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\Project\EditController::__invoke
     */
    public function test front project edit response is successful update(): void
    {
        self::setUpClient(User::ROLE_USER);

        /** @var Project $project */
        $project = self::$em
            ->getRepository(Project::class)
            ->findOneBy([]);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('project_edit', [
                'id' => $project->getId(),
            ])
        );

        self::assertResponseIsSuccessful();

        $editedTitle = StringUtils::stringToLength('edited title', 15);
        $editedContent = StringUtils::stringToLength('edited content', 75);
        $categories = self::$em->getRepository(Category::class)->createQueryBuilder('category')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
        $categoriesArray = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $categoriesArray[] = $category->getTitle();
        }
        $categoriesAsString = implode(',', $categoriesArray);

        $values['project_edit']['title'] = $editedTitle;
        $values['project_edit']['content'] = $editedContent;
        $values['project_edit']['categories'] = $categoriesAsString;
        $values['project_edit']['_token'] = self::$container
            ->get('security.csrf.token_manager')
            ->getToken('project_edit')
            ->getValue();

        self::$client->request(
            Request::METHOD_POST,
            self::$router->generate('project_edit', [
                'id' => $project->getId(),
            ]),
            $values
        );

        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertNotEmpty(
            self::$em
                ->getRepository(Project::class)
                ->findBy(['title' => $editedTitle, 'content' => $editedContent])
        );
    }
}
