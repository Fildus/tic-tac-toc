<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EncodePassword
{
    /** @required */
    public UserPasswordEncoderInterface $encoder;

    public function encode(object $entity): void
    {
        if (!$entity instanceof User) {
            return;
        }

        $entity->setPassword(
            $this->encoder->encodePassword($entity, $entity->getPassword())
        );
    }
}
