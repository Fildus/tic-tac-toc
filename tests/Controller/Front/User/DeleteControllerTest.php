<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front\User;

use App\Entity\User;
use App\Tests\FixturesTrait;
use Doctrine\DBAL\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Front\User\DeleteController
 *
 * @group User\DeleteControllerTest
 */
class DeleteControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\User\DeleteController::__invoke
     *
     * @throws ConnectionException
     */
    public function test_frontUserDelete_deleteUser(): void
    {
        self::setUpClient(User::ROLE_USER);

        /** @var User $user */
        $user = self::$em->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);

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
