<?php

namespace App\Tests\Controller\Front;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\Front\UserController
 * @group UserFrontControllerTest
 */
class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\UserController::new
     */
    public function test_frontUserNew_responseIsSuccessful(): void
    {
        self::setUpClient(User::IS_AUTHENTICATED_ANONYMOUSLY);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('front_user_new')
        );

        self::assertResponseIsSuccessful();
    }

    /**
     * @covers \App\Controller\Front\UserController::new
     */
    public function test_frontUserNew_responseIsSuccessful_createNew(): void
    {
        self::setUpClient(User::IS_AUTHENTICATED_ANONYMOUSLY);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('front_user_new')
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

    /**
     * @covers \App\Controller\Front\UserController::edit
     */
    public function test_frontUserEdit_responseIsSuccessful(): void
    {
        self::setUpClient(User::ROLE_USER);

        $user = self::$em
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@user.com']);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('front_user_edit', ['id' => $user->getId()])
        );

        self::assertResponseIsSuccessful();
    }

    /**
     * @covers \App\Controller\Front\UserController::edit
     */
    public function test_frontUserEdit_responseIsSuccessful_updateEmail(): void
    {
        self::setUpClient(User::ROLE_USER);

        $user = self::$em
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@user.com']);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('front_user_edit', ['id' => $user->getId()])
        );

        self::assertResponseIsSuccessful();

        $email = 'user@user.somethingNew';
        $form = $crawler->filter('form[name="edit_account"]')->selectButton('save')->form();
        $values = $form->getPhpValues();
        $values['edit_account']['email'] = $email;

        self::$client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertNotEmpty(
            self::$em
                ->getRepository(User::class)
                ->findBy(['email' => $email])
        );
    }

    /**
     * @covers \App\Controller\Front\UserController::edit
     */
    public function test_frontUserEdit_responseIsSuccessful_updatePassword(): void
    {
        $email = 'user@user.com';

        self::setUpClient(User::ROLE_USER);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => $email]);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('front_user_edit', ['id' => $user->getId()])
        );
        self::assertResponseIsSuccessful();

        $oldPasswordHashed = $user->getPassword();
        $newPassword = 'newPassword';

        $form = $crawler->filter('form[name="edit_account"]')->selectButton('save')->form();
        $values = $form->getPhpValues();
        $values['update_password']['password']['first'] = $newPassword;
        $values['update_password']['password']['second'] = $newPassword;
        self::$client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        self::$client->followRedirect();
        self::assertResponseIsSuccessful();

        /** @var User $user */
        $user = self::$em
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);
        $newPasswordHashed = $user->getPassword();

        static::assertNotEquals($newPassword, $newPasswordHashed);
        static::assertNotEquals($oldPasswordHashed, $newPasswordHashed);
    }

    /**
     * @covers \App\Controller\Front\UserController::delete
     */
    public function test_frontUserDelete_deleteUser(): void
    {
        self::setUpClient(User::ROLE_USER);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        $crawler = self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('front_user_edit', ['id' => $user->getId()])
        );

        $form = $crawler
            ->filter('form[name="delete_user"]')
            ->selectButton('Supprimer le compte')
            ->form();
        $values = $form->getPhpValues();
        self::$client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        self::$client->followRedirect();
        static::assertEquals(Response::HTTP_FOUND, self::$client->getResponse()->getStatusCode());
        static::assertNull(self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']));
    }
}
