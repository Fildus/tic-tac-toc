<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginResponse(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/login');
        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request(Request::METHOD_POST, '/login');
        static::assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $client->request('whatever', '/login');
        static::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
    }
}
