<?php

declare(strict_types=1);

namespace App\Tests\Controller\Front\Project;

use App\Entity\Project;
use App\Entity\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Controller\Front\Project\DeleteController
 *
 * @group Project\DeleteControllerTest
 */
class DeleteControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @covers \App\Controller\Front\Project\DeleteController::__invoke
     */
    public function test_frontProjectDelete_deleteProject(): void
    {
        self::setUpClient(User::ROLE_USER);

        /** @var Project $project */
        $project = self::$em->getRepository(Project::class)->findOneBy([]);

        $values['_method'] = Request::METHOD_DELETE;
        $values['_token'] = self::$container
            ->get('security.csrf.token_manager')
            ->getToken('delete'.$project->getId())
            ->getValue();

        self::$client->request(
            Request::METHOD_POST,
            self::$router->generate('project_delete', ['id' => $project->getId()]),
            $values
        );

        self::assertResponseRedirects();
        static::assertNull(self::$em->getRepository(Project::class)->findOneBy(['id' => $project->getId()]));
    }
}
