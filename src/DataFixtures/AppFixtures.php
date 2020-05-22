<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Utils\StringUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        $this->createCategories($manager);

        $simpleUser = $this->createUser('user@user.com', [User::ROLE_USER]);
        $simpleUser->addProject($this->createProject());
        $manager->persist($simpleUser);

        $adminUser = $this->createUser('admin@admin.com', [User::ROLE_ADMIN]);
        $adminUser->addProject($this->createProject());
        $manager->persist($adminUser);

        for ($i = 0; $i < rand(10, 15); ++$i) {
            $user = $this->createUser(StringUtils::stringToLength($this->faker->email.$i, 50), [User::ROLE_USER]);

            for ($j = 0; $j < rand(2, 5); ++$j) {
                $project = $this->createProject();
                $project->setUser($user);

                $manager->persist($project);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function createUser(string $mail, array $role): User
    {
        $user = new User();
        $user
            ->setEmail($mail)
            ->setRoles($role)
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$aVLlSqPcGeRTOrFiW8ipqA$4zVgVZjAjDAsNvLqDRtiG93px0J5p4+7HfuM3NRnT1g');

        return $user;
    }

    public function createProject(): Project
    {
        $project = new Project();
        $project
            ->setTitle(StringUtils::stringToLength($this->faker->slug, 25))
            ->setContent(StringUtils::stringToLength($this->faker->realText(400), 400));

        return $project;
    }

    private function createCategories(ObjectManager $manager): void
    {
        $php = new Category();
        $php->setTitle('php');

        $symfony = new Category();
        $symfony->setTitle('symfony');

        $drupal = new Category();
        $drupal->setTitle('drupal');

        $laravel = new Category();
        $laravel->setTitle('laravel');

        $javascript = new Category();
        $javascript->setTitle('javascript');

        $vuejs = new Category();
        $vuejs->setTitle('vuejs');

        $reactjs = new Category();
        $reactjs->setTitle('reactjs');

        $emberjs = new Category();
        $emberjs->setTitle('emberjs');

        $cms = new Category();
        $cms->setTitle('cms');

        $wordpress = new Category();
        $wordpress->setTitle('wordpress');

        /** @var CategoryRepository $rep */
        $rep = $manager->getRepository(Category::class);

        $rep->persistAsFirstChild($php);
        $rep->persistAsFirstChildOf($symfony, $php);
        $rep->persistAsFirstChildOf($drupal, $php);
        $rep->persistAsFirstChildOf($laravel, $php);

        $rep->persistAsFirstChild($javascript);
        $rep->persistAsFirstChildOf($vuejs, $javascript);
        $rep->persistAsFirstChildOf($reactjs, $javascript);
        $rep->persistAsFirstChildOf($emberjs, $javascript);

        $rep->persistAsFirstChild($cms);
        $rep->persistAsFirstChildOf($wordpress, $cms);
    }
}
