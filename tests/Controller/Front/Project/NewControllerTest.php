<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front\Project;

use App\Entity\Project;
use App\Entity\User;
use App\Tests\FixturesTrait;
use Doctrine\DBAL\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Front\Project\NewController
 *
 * @group Project\NewControllerTest
 */
class NewControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\Project\NewController::__invoke
     *
     * @throws ConnectionException
     */
    public function test_frontProjectNew_responseIsSuccessful_createNew(): void
    {
        self::setUpClient(User::ROLE_USER);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('project_new')
        );

        self::assertResponseIsSuccessful();

        $newTitle = 'new title';
        $newContent = 'new content';
        $values['project_new']['title'] = $newTitle;
        $values['project_new']['content'] = $newContent;
        $values['project_new']['_token'] = self::$container
            ->get('security.csrf.token_manager')
            ->getToken('project_new')
            ->getValue();

        self::$client->request(
            Request::METHOD_POST,
            self::$router->generate('project_new'),
            $values
        );

        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertNotEmpty(
            self::$em
                ->getRepository(Project::class)
                ->findBy(['title' => $newTitle, 'content' => $newContent])
        );
    }
}
