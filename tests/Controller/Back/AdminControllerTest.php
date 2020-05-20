<?php

declare(strict_types=1);

namespace App\Tests\Controller\Back;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\Admin\DashboardController
 *
 * @group AdminControllerTest
 */
class AdminControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Admin\DashboardController::index
     */
    public function test admin response is successful with admin(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin', ['route' => 'dashboard'])
        );
        self::assertResponseRedirects();
    }

    /**
     * @covers \App\Controller\Admin\DashboardController::index
     */
    public function test admin response is failed with user(): void
    {
        self::setUpClient(User::ROLE_USER);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin')
        );
        static::assertEquals(Response::HTTP_FORBIDDEN, self::$client->getResponse()->getStatusCode());
    }

    /**
     * @covers \App\Controller\Admin\DashboardController::index
     */
    public function test admin response is failed with anonymous(): void
    {
        self::setUpClient(User::IS_AUTHENTICATED_ANONYMOUSLY);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('admin')
        );
        static::assertResponseRedirects(self::$router->generate('app_login'));
    }
}
