<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Doctrine\DBAL\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Front\User\RegistrationController
 * @covers \App\Controller\Front\User\EditController
 * @covers \App\Controller\Front\User\DeleteController
 * @group UserFrontControllerTest
 */
class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\User\DeleteController::__invoke
     *
     * @throws ConnectionException
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
     * @covers \App\Controller\Front\User\DeleteController::__invoke
     *
     * @throws ConnectionException
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
     * @covers \App\Controller\Front\User\DeleteController::__invoke
     *
     * @throws ConnectionException
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
     * @covers \App\Controller\Front\User\EditController::__invoke
     *
     * @throws ConnectionException
     */
    public function test_frontUserEdit_responseIsSuccessful_updateEmail(): void
    {
        self::setUpClient(User::ROLE_USER);

        /** @var User $user */
        $user = self::$em
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@user.com']);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('front_user_edit', ['id' => $user->getId()])
        );

        self::assertResponseIsSuccessful();

        $email = 'user@user.somethingNew';
        $values['edit_account']['email'] = $email;
        $values['edit_account']['_token'] = self::$container
            ->get('security.csrf.token_manager')
            ->getToken('edit_account')
            ->getValue();

        self::$client->request(
            Request::METHOD_POST,
            self::$router->generate('front_user_edit', ['id' => $user->getId()]),
            $values
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
     * @covers \App\Controller\Front\User\EditController::__invoke
     *
     * @throws ConnectionException
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

        $values['update_password']['password']['first'] = $newPassword;
        $values['update_password']['password']['second'] = $newPassword;
        $values['update_password']['_token'] = self::$container
            ->get('security.csrf.token_manager')
            ->getToken('update_password')
            ->getValue();

        self::$client->request(
            Request::METHOD_POST,
            self::$router->generate('front_user_edit', ['id' => $user->getId()]),
            $values
        );

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
     * @covers \App\Controller\Front\User\DeleteController::__invoke
     *
     * @throws ConnectionException
     */
    public function test_frontUserEdit_deleteUser(): void
    {
        self::setUpClient(User::ROLE_USER);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

        self::$client->request(
            Request::METHOD_GET,
            self::$router->generate('front_user_edit', ['id' => $user->getId()])
        );

        self::assertResponseIsSuccessful();

        $values['_method'] = Request::METHOD_DELETE;
        $values['_token'] = self::$container
            ->get('security.csrf.token_manager')
            ->getToken('delete'.$user->getId())
            ->getValue();

        self::$client->request(
            Request::METHOD_POST,
            self::$router->generate('front_user_delete', ['id' => $user->getId()]),
            $values
        );

        self::assertResponseRedirects();
        static::assertNull(self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']));
    }
}
