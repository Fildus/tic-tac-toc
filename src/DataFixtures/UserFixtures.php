<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class UserFixtures extends Fixture
{
    private ObjectManager $manager;
    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();

        $this->createUser('user@user.com', [User::ROLE_USER]);
        $this->createUser('admin@admin.com', [User::ROLE_ADMIN]);

        for ($i = 0; $i < 11; ++$i) {
            $this->createUser($this->faker->email.$i, [User::ROLE_USER]);
        }

        $this->manager->flush();
    }

    public function createUser(string $mail, array $role): void
    {
        $user = new User();
        $user
            ->setEmail($mail)
            ->setRoles($role)
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$aVLlSqPcGeRTOrFiW8ipqA$4zVgVZjAjDAsNvLqDRtiG93px0J5p4+7HfuM3NRnT1g');

        $this->manager->persist($user);
    }
}
