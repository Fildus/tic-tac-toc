<?php

namespace App\DataFixtures\Dev;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordEncoderInterface $passwordEncoder;

    private ObjectManager $manager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createUser('user@user.com', [User::ROLE_USER], 'test');
        $this->createUser('admin@admin.com', [User::ROLE_ADMIN], 'test');
        $manager->flush();
    }

    public function createUser(string $mail, array $role, string $password)
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

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['dev'];
    }
}
