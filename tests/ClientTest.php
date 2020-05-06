<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ClientTest extends WebTestCase
{
    public static function createAuthorizedClient(string $role): KernelBrowser
    {
        $roles = [
            User::ROLE_USER => 'user@user.com',
            User::ROLE_ADMIN => 'admin@admin.com',
        ];

        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');

        $person = $container->get('doctrine')->getRepository(User::class)->findOneBy(['email' => $roles[$role]]);

        $token = new UsernamePasswordToken($person, null, 'main', (array) $person->getRoles());

        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    public function test_authUser(): void
    {
        $client = self::createAuthorizedClient(User::ROLE_USER);
        $security = unserialize($client->getContainer()->get('session')->get('_security_main'));

        static::assertEquals(UsernamePasswordToken::class, get_class($security));
        static::assertTrue(in_array(User::ROLE_USER, $security->getUser()->getRoles(), true));
    }

    public function test_authAdmin(): void
    {
        $client = self::createAuthorizedClient(User::ROLE_ADMIN);
        $security = unserialize($client->getContainer()->get('session')->get('_security_main'));

        static::assertEquals(UsernamePasswordToken::class, get_class($security));

        static::assertTrue(in_array(User::ROLE_ADMIN, $security->getUser()->getRoles(), true));
    }
}
