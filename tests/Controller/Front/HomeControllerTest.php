<?php

namespace App\Tests\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group HomeControllerTest
 */
class HomeControllerTest extends WebTestCase
{
    public function test_home_responseIsSuccessful(): void
    {
        $client = static::createClient();

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('home')
        );
        self::assertResponseIsSuccessful();
    }

    public function test_home_responseIsFailed(): void
    {
        $client = static::createClient();

        $client->request(
            'whatever',
            $client->getContainer()->get('router')->generate('home')
        );

        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
