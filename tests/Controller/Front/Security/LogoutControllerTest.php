<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front\Security;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @covers \App\Controller\Front\Security\LogoutController
 *
 * @group LogoutControllerTest
 */
class LogoutControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\Security\LogoutController::__invoke
     */
    public function test logout is successful(): void
    {
        self::setUpClient(User::ROLE_ADMIN);
        self::$client->request(Request::METHOD_GET, self::$router->generate('home'));

        /** @var UsernamePasswordToken $security */
        $security = unserialize(self::$client->getRequest()->getSession()->get('_security_main', false));

        /** @var User $user */
        $user = $security->getUser();
        static::assertTrue(in_array(User::ROLE_ADMIN, $user->getRoles(), true));

        self::$client->request(Request::METHOD_GET, self::$router->generate('app_logout'));
        static::assertFalse(self::$client->getRequest()->getSession()->get('_security_main', false));
    }
}
