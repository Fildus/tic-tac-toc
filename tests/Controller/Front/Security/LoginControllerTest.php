<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front\Security;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Doctrine\DBAL\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\Front\Security\LoginController
 *
 * @group LoginControllerTest
 */
class LoginControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\Security\LoginController::__invoke
     *
     * @throws ConnectionException
     */
    public function test_loginResponse(): void
    {
        self::setUpClient(User::IS_AUTHENTICATED_ANONYMOUSLY);

        $login = self::$router->generate('app_login');

        self::$client->request(Request::METHOD_GET, $login);
        self::assertResponseIsSuccessful();

        self::$client->request(Request::METHOD_POST, $login);
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        self::$client->request('whatever', $login);
        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * @covers \App\Controller\Front\Security\LoginController::__invoke
     *
     * @throws ConnectionException
     */
    public function test_login_isSuccessful(): void
    {
        self::setUpClient(User::IS_AUTHENTICATED_ANONYMOUSLY);
        self::$client->followRedirects();

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$client->getContainer()->get('router')->generate('app_login')
        );
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sign in')->form();
        $values = $form->getPhpValues();
        $values['email'] = 'admin@admin.com';
        $values['password'] = 'test';

        self::$client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        self::assertResponseIsSuccessful();
        self::assertRouteSame('home');

        $security = self::$client->getRequest()->getSession()->get('_security_main', false);
        static::assertNotFalse($security && unserialize($security)->getUser());
    }

    /**
     * @covers \App\Controller\Front\Security\LoginController::__invoke
     *
     * @throws ConnectionException
     */
    public function test_login_isFailed(): void
    {
        self::setUpClient(User::IS_AUTHENTICATED_ANONYMOUSLY);

        $crawler = self::$client->request(Request::METHOD_GET, self::$router->generate('app_login'));
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sign in')->form();
        $values = $form->getPhpValues();
        $values['email'] = 'something@something.com';
        $values['password'] = 'test';

        self::$client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertRouteSame('app_login');

        $security = self::$client->getRequest()->getSession()->get('_security_main', false);
        static::assertFalse($security && unserialize($security)->getUser());
    }
}
