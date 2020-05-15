<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front\User;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Doctrine\DBAL\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Front\User\EditController
 *
 * @group User\EditControllerTest
 */
class EditControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\User\EditController::__invoke
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

        self::$client->request(
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
}
