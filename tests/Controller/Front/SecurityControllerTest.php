<?php

namespace App\Tests\Controller\Front;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @covers \App\Controller\Front\SecurityController
 * @group SecurityControllerTest
 */
class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\SecurityController::login
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
     * @covers \App\Controller\Front\SecurityController::login
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
     * @covers \App\Controller\Front\SecurityController::login
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

    /**
     * @covers \App\Controller\Front\SecurityController::logout
     */
    public function test_logout_isSuccessful(): void
    {
        self::setUpClient(User::ROLE_ADMIN);
        self::$client->request(Request::METHOD_GET, self::$router->generate('home'));

        /** @var UsernamePasswordToken $security */
        $security = unserialize(self::$client->getRequest()->getSession()->get('_security_main', false));

        /** @var User $user */
        $user = $security->getUser();
        static::assertTrue(in_array(User::ROLE_ADMIN, $user->getRoles(), true));

        self::$client->request(Request::METHOD_GET, self::$router->generate('app_logout'));
        static::assertFalse(self::$client->getRequest()->getSession()->get('_security_main', false));
    }
}
