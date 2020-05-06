<?php

namespace App\Tests\Controller\Front;

use App\Entity\User;
use App\Tests\ClientTest;
use App\Tests\Database;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group UserControllerTest
 */
class UserControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        Database::reload();
    }

    public function test_frontUserNew_responseIsSuccessful(): void
    {
        $client = static::createClient();

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('front_user_new')
        );

        self::assertResponseIsSuccessful();
    }

    public function test_frontUserNew_responseIsSuccessful_createNew(): void
    {
        $client = static::createClient();

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('front_user_new')
        );

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Créer un compte !')->form();
        $values = $form->getPhpValues();
        $email = 'somethingNew@somethingNew.com';
        $values['create_account']['email'] = $email;
        $values['create_account']['password'] = 'test';

        $client->request(
            $form->getMethod(),
            $form->getUri(),
            $values, $form->getPhpFiles()
        );

        $client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertNotEmpty(
            $client
                ->getContainer()
                ->get('doctrine')
                ->getRepository(User::class)
                ->findBy(['email' => $email])
        );
    }

    public function test_frontEdit_responseIsSuccessful(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_USER);

        $user = $client
            ->getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@user.com']);

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('front_user_edit', ['id' => $user->getId()])
        );

        self::assertResponseIsSuccessful();
    }

    public function test_frontUserEdit_responseIsSuccessful_updateEmail(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_USER);

        $user = $client
            ->getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@user.com']);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('front_user_edit', ['id' => $user->getId()])
        );

        self::assertResponseIsSuccessful();

        $email = 'user@user.somethingNew';
        $form = $crawler->filter('form[name="edit_account"]')->selectButton('save')->form();
        $values = $form->getPhpValues();
        $values['edit_account']['email'] = $email;

        $client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        $client->followRedirect();
        self::assertResponseIsSuccessful();

        static::assertNotEmpty(
            $client
                ->getContainer()
                ->get('doctrine')
                ->getRepository(User::class)
                ->findBy(['email' => $email])
        );
    }

    public function test_frontUserEdit_responseIsSuccessful_updatePassword(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_USER);

        $email = 'user@user.com';

        /** @var User $user */
        $user = $client
            ->getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('front_user_edit', ['id' => $user->getId()])
        );

        self::assertResponseIsSuccessful();

        $oldPasswordHashed = $user->getPassword();
        $newPassword = 'newPassword';

        $form = $crawler->filter('form[name="edit_account"]')->selectButton('save')->form();
        $values = $form->getPhpValues();
        $values['update_password']['password']['first'] = $newPassword;
        $values['update_password']['password']['second'] = $newPassword;

        $client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        $client->followRedirect();

        self::assertResponseIsSuccessful();

        /** @var User $user */
        $user = $client
            ->getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);
        $newPasswordHashed = $user->getPassword();

        static::assertNotEquals($newPassword, $newPasswordHashed);
        static::assertNotEquals($oldPasswordHashed, $newPasswordHashed);
    }

    public function test_frontUserDelete_DeleteUser(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_USER);

        /** @var User $user */
        $user = $client
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@user.com']);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('front_user_edit', ['id' => $user->getId()])
        );

        $form = $crawler->filter('form[name="delete_user"]')->selectButton('Supprimer le compte')->form();
        $values = $form->getPhpValues();

        $client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        $client->followRedirect();
        static::assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        static::assertNull(
            $client
                ->getContainer()
                ->get('doctrine')
                ->getManager()
                ->getRepository(User::class)
                ->findOneBy(['email' => 'user@user.com'])
        );
    }
}
