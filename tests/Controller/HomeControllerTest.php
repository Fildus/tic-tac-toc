<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase
{
    public function testHomeResponse(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/');
        $this->assertResponseIsSuccessful();

        $client->request('whatever', '/');
        $this->assertEquals($client->getResponse()->getStatusCode(), Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
