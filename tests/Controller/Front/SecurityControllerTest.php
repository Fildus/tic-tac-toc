<?php

namespace App\Tests\Controller\Front;

use App\Entity\User;
use App\Tests\ClientTest;
use App\Tests\Database;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginResponse(): void
    {
        Database::reload();
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/login');
        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request(Request::METHOD_POST, '/login');
        static::assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $client->request('whatever', '/login');
        static::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
    }

    public function testLogin(): void
    {
        Database::reload();
        $client = static::createClient();
        $client->followRedirects();

        $client->request(Request::METHOD_GET, $client->getContainer()->get('router')->generate('home'));
        $crawler = $client->request(Request::METHOD_GET, $client->getContainer()->get('router')->generate('app_login'));

        $form = $crawler->selectButton('Sign in')->form();

        $values = $form->getPhpValues();
        $values['email'] = 'admin@admin.com';
        $values['password'] = 'test';

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        static::assertStringContainsString('Tic tac toc', (string) $client->getResponse()->getContent());
        static::assertStringContainsString('home', (string) $client->getResponse()->getContent());

        $security = $client->getRequest()->getSession()->get('_security_main', false);

        static::assertNotFalse($security && unserialize($security)->getUser());
    }

    public function testLogout(): void
    {
        Database::reload();
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);
        $client->followRedirects();

        $client->request(Request::METHOD_GET, $client->getContainer()->get('router')->generate('home'));

        $security = $client->getRequest()->getSession()->get('_security_main', false);
        static::assertNotFalse($security);

        /** @var UsernamePasswordToken $security */
        $security = unserialize($security);

        /** @var User $user */
        $user = $security->getUser();

        $inArrayAdmin = in_array(User::ROLE_ADMIN, $user->getRoles(), true);

        static::assertTrue($inArrayAdmin);

        $client->request(Request::METHOD_GET, $client->getContainer()->get('router')->generate('app_logout'));

        $security = $client->getRequest()->getSession()->get('_security_main', false);
        static::assertFalse($security);
    }
}
