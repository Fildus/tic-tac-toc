<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    private ObjectManager $manager;
    private Generator $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();

        $this->createUser('user@user.com', [User::ROLE_USER], 'test');
        $this->createUser('admin@admin.com', [User::ROLE_ADMIN], 'test');

        for ($i = 0; $i < 11; ++$i) {
            $this->createUser($this->faker->email.$i, [User::ROLE_USER], 'test');
        }

        $this->manager->flush();
    }

    public function createUser(string $mail, array $role, string $password): void
    {
        $user = new User();
        $user
            ->setEmail($mail)
            ->setRoles($role)
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $password
            ));

        $this->manager->persist($user);
    }
}
