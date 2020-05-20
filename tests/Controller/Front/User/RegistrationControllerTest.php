<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front\User;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Front\User\RegistrationController
 * @covers \App\Controller\Front\User\DeleteController
 *
 * @group User\RegistrationControllerTest
 */
class RegistrationControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\User\RegistrationController::__invoke
     */
    public function test front user registration response is successful(): void
    {
        self::setUpClient(User::IS_AUTHENTICATED_ANONYMOUSLY);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('front_user_registration')
        );

        self::assertResponseIsSuccessful();
    }

    /**
     * @covers \App\Controller\Front\User\RegistrationController::__invoke
     */
    public function test front user registration response is successful create new(): void
    {
        self::setUpClient(User::IS_AUTHENTICATED_ANONYMOUSLY);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('front_user_registration')
        );

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Créer un compte !')->form();
        $values = $form->getPhpValues();
        $email = 'somethingNew@somethingNew.com';
        $values['create_account']['email'] = $email;
        $values['create_account']['password'] = 'test';

        self::$client->request(
            $form->getMethod(),
            $form->getUri(),
            $values, $form->getPhpFiles()
        );

        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertNotEmpty(self::$em->getRepository(User::class)->findBy(['email' => $email]));
    }
}
