<?php

namespace App\Tests\Controller\Front;

use App\Entity\User;
use App\Tests\ClientTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @group SecurityControllerTest
 */
class SecurityControllerTest extends WebTestCase
{
    public function test_loginResponse(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/login');
        self::assertResponseIsSuccessful();

        $client->request(Request::METHOD_POST, '/login');
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->request('whatever', '/login');
        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function test_login_isSuccessful(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('app_login')
        );
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sign in')->form();

        $values = $form->getPhpValues();
        $values['email'] = 'admin@admin.com';
        $values['password'] = 'test';

        $client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        self::assertResponseIsSuccessful();

        self::assertRouteSame('home');

        $security = $client->getRequest()->getSession()->get('_security_main', false);
        static::assertNotFalse($security && unserialize($security)->getUser());
    }

    public function test_login_isFailed(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('app_login')
        );
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sign in')->form();

        $values = $form->getPhpValues();
        $values['email'] = 'something@something.com';
        $values['password'] = 'test';

        $client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        self::assertResponseIsSuccessful();

        self::assertRouteSame('app_login');

        $security = $client->getRequest()->getSession()->get('_security_main', false);
        static::assertFalse($security && unserialize($security)->getUser());
    }

    public function test_logout_isSuccessful(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_ADMIN);
        $client->followRedirects();

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('home')
        );

        $security = $client->getRequest()->getSession()->get('_security_main', false);
        static::assertNotFalse($security);

        /** @var UsernamePasswordToken $security */
        $security = unserialize($security);

        /** @var User $user */
        $user = $security->getUser();

        static::assertTrue(in_array(User::ROLE_ADMIN, $user->getRoles(), true));

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('app_logout')
        );

        static::assertFalse(
            $client->getRequest()->getSession()->get('_security_main', false)
        );
    }
}
