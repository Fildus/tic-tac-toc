<?php

namespace App\Tests\Controller\Front;

use App\Tests\Database;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        Database::reload();
    }

    public function testHomeResponse(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/');
        self::assertResponseIsSuccessful();

        $client->request('whatever', '/');
        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
    }
}
