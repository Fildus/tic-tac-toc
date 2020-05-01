<?php

namespace App\Tests\Controller\Front;

use App\Entity\User;
use App\Tests\ClientTest;
use App\Tests\Database;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        Database::reload();
    }

    public function testNewResponse(): void
    {
        $client = static::createClient();

        $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('front_user_new')
        );

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testAdminNew(): void
    {
        $client = static::createClient();

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('front_user_new')
        );

        $client->followRedirects();

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Créer un compte !')->form();

        $values = $form->getPhpValues();

        $email = 'somethingNew@somethingNew.com';

        $values['create_account']['email'] = $email;
        $values['create_account']['password'] = 'test';

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        static::assertNotEmpty($client->getContainer()->get('doctrine')->getRepository(User::class)->findBy(['email' => $email]));
    }

    public function testAdminEdit(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_USER);
        $client->followRedirects();

        $userDb = $client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
        $data = $userDb->findOneBy(['email' => 'user@user.com']);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('front_user_edit', ['id' => $data->getId()])
        );

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $email = 'user@user.somethingNew';

        $this->changeEmail($crawler, $client, $email);
        $this->changePassword($crawler, $client, $email);
    }

    public function changeEmail(Crawler $crawler, KernelBrowser $client, string $email): void
    {
        $form = $crawler->filter('form[name="edit_account"]')->selectButton('save')->form();

        $values = $form->getPhpValues();

        $values['edit_account']['email'] = $email;

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        static::assertNotEmpty($client->getContainer()->get('doctrine')->getRepository(User::class)->findBy(['email' => $email]));
    }

    public function changePassword(Crawler $crawler, KernelBrowser $client, string $email): void
    {
        /** @var User $user */
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => $email]);
        $oldPassword = $user->getPassword();

        $form = $crawler->filter('form[name="update_password"]')->selectButton('save')->form();

        $values = $form->getPhpValues();
        $passwordNotHash = 'somethingNew';
        $values['update_password']['password']['first'] = 'somethingNew';
        $values['update_password']['password']['second'] = 'somethingNew';

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        /** @var User $user */
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => $email]);
        $newPassword = $user->getPassword();

        static::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        static::assertNotEquals($newPassword, $oldPassword);
        static::assertNotEquals($newPassword, $passwordNotHash);
    }

    public function testAdminDeleteResponseSuccess(): void
    {
        $client = ClientTest::createAuthorizedClient(User::ROLE_USER);

        $userDb = $client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);

        /** @var User $user */
        $user = $userDb->findOneBy(['email' => 'user@user.com']);

        $crawler = $client->request(
            Request::METHOD_GET,
            $client->getContainer()->get('router')->generate('front_user_edit', ['id' => $user->getId()])
        );

        $form = $crawler->filter('form[name="delete_user"]')->selectButton('Delete')->form();
        $values = $form->getPhpValues();
        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $client->followRedirect();

        static::assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        static::assertNull($userDb->findOneBy(['email' => 'user@user.com']));
    }
}
