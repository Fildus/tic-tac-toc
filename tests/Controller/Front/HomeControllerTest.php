<?php

namespace App\Tests\Controller\Front;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\Front\HomeController
 * @group HomeControllerTest
 */
class HomeControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\HomeController::__invoke
     */
    public function test_home_responseIsSuccessful(): void
    {
        self::setUpClient(User::IS_AUTHENTICATED_ANONYMOUSLY);
        self::$client->request(Request::METHOD_GET, self::$router->generate('home'));
        self::assertResponseIsSuccessful();
    }

    /**
     * @covers \App\Controller\Front\HomeController::__invoke
     */
    public function test_home_responseIsFailed(): void
    {
        self::setUpClient(User::IS_AUTHENTICATED_ANONYMOUSLY);
        self::$client->request('whatever', self::$router->generate('home'));
        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
