<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EncodePassword
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function encode(object $entity): void
    {
        if (!$entity instanceof User) {
            return;
        }

        if (!$this->encoder->needsRehash($entity)) {
            return;
        }

        $entity->setPassword(
            $this->encoder->encodePassword($entity, $entity->getPassword())
        );
    }
}
