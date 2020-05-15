<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait FixturesTrait
{
    private static KernelBrowser $client;
    private static EntityManagerInterface $em;
    private static Router $router;

    /**
     * @throws ConnectionException
     */
    private static function setUpClient(string $role): void
    {
        self::$client = User::IS_AUTHENTICATED_ANONYMOUSLY === $role ?
            WebTestCase::createClient() :
            self::createAuthorizedClient($role);
        self::$client->disableReboot();
        self::$em = static::$kernel->getContainer()->get('doctrine')->getManager();
        self::$em->getConnection()->beginTransaction();
        self::$em->getConnection()->setAutoCommit(false);
        $router = self::$client->getContainer()->get('router');
        if ($router instanceof Router) {
            self::$router = $router;
        }
    }

    /**
     * After each test, a rollback reset the state of
     * the database.
     *
     * @throws ConnectionException
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        if (self::$em->getConnection()->isTransactionActive()) {
            self::$em->getConnection()->rollback();
            self::$em->getConnection()->close();
        }
    }

    public static function createAuthorizedClient(string $role): KernelBrowser
    {
        $roles = [User::ROLE_USER => 'user@user.com', User::ROLE_ADMIN => 'admin@admin.com'];
        $client = WebTestCase::createClient();
        $container = WebTestCase::$kernel->getContainer();
        $session = $container->get('session');
        $person = $container->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => $roles[$role]]);
        $token = new UsernamePasswordToken($person, null, 'main', (array) $person->getRoles());

        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }
}
