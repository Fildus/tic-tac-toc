<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    private ObjectManager $manager;
    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();

        for ($i = 0; $i < 11; ++$i) {
            $this->createProject();
        }

        $manager->flush();
    }

    public function createProject(): void
    {
        $project = new Project();
        $project
            ->setTitle($this->faker->slug)
            ->setContent($this->faker->text);

        $this->manager->persist($project);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
