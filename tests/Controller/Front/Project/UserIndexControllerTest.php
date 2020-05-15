<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front\Project;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Doctrine\DBAL\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Front\Project\UserIndexController
 *
 * @group Project\UserIndexControllerTest
 */
class UserIndexControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\Project\UserIndexController::__invoke
     *
     * @throws ConnectionException
     */
    public function test_frontProjectUserIndex_responseIsSuccessful(): void
    {
        self::setUpClient(User::ROLE_USER);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('project_user_index')
        );

        self::assertResponseIsSuccessful();
    }
}
