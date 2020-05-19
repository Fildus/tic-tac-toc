<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front\Project;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Front\Project\IndexController
 *
 * @group Project\IndexControllerTest
 */
class IndexControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\Project\IndexController::__invoke
     */
    public function test_frontProjectIndex_responseIsSuccessful(): void
    {
        self::setUpClient(User::ROLE_USER);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('project_index')
        );

        self::assertResponseIsSuccessful();
    }
}
