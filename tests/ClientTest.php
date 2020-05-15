<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use Doctrine\DBAL\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @group ClientTest
 */
class ClientTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @throws ConnectionException
     */
    public function test_authUser(): void
    {
        self::setUpClient(User::ROLE_USER);

        $security = unserialize(self::$client->getContainer()->get('session')->get('_security_main'));

        static::assertEquals(UsernamePasswordToken::class, get_class($security));
        static::assertTrue(in_array(User::ROLE_USER, $security->getUser()->getRoles(), true));
    }

    /**
     * @throws ConnectionException
     */
    public function test_authAdmin(): void
    {
        self::setUpClient(User::ROLE_ADMIN);

        $security = unserialize(self::$client->getContainer()->get('session')->get('_security_main'));

        static::assertEquals(UsernamePasswordToken::class, get_class($security));
        static::assertTrue(in_array(User::ROLE_ADMIN, $security->getUser()->getRoles(), true));
    }
}
