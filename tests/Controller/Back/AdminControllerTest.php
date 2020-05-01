<?php

namespace App\Tests\Controller\Back;

use App\Entity\User;
use App\Tests\ClientTest;
use App\Tests\Database;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        Database::reload();
    }

    public function testAdminResponseSuccessAdmin(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin', [
                'route' => 'dashboard',
            ])
        );

        static::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAdminResponseFailUser(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_USER);

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin')
        );

        static::assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testAdminResponseFailAnonymous(): void
    {
        $client = static::createClient();

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('admin')
        );

        static::assertResponseRedirects($client->getContainer()->get('router')->generate('app_login'));
    }
}
