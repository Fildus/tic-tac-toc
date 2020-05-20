<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @group ClientTest
 */
class ClientTest extends WebTestCase
{
    use FixturesTrait;

    public function test auth user(): void
    {
        self::setUpClient(User::ROLE_USER);

        $security = unserialize(self::$client->getContainer()->get('session')->get('_security_main'));

        static::assertEquals(UsernamePasswordToken::class, get_class($security));
        static::assertTrue(in_array(User::ROLE_USER, $security->getUser()->getRoles(), true));
    }

    public function test auth admin(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        $security = unserialize(self::$client->getContainer()->get('session')->get('_security_main'));

        static::assertEquals(UsernamePasswordToken::class, get_class($security));
        static::assertTrue(in_array(User::ROLE_ADMIN, $security->getUser()->getRoles(), true));
    }
}
