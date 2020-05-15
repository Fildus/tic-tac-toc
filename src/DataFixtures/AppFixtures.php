<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $projects = $manager->getRepository(Project::class)->findAll();
        array_shift($projects);
        shuffle($projects);

        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < rand(0, 3); ++$i) {
                $project = array_shift($projects);
                if (null !== $project) {
                    $user->addProject($project);
                    $manager->persist($user);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ProjectFixtures::class,
        ];
    }
}
