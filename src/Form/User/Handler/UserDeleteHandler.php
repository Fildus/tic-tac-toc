<?php

declare(strict_types=1);

namespace App\Form\User\Handler;

use App\Infrastructure\DeleteHandler\AbstractDeleteBuilder;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserDeleteHandler extends AbstractDeleteBuilder
{
    /** @required */
    public FlashBagInterface $flashBag;
    /** @required */
    public TokenStorageInterface $tokenStorage;

    public function onSuccess(object $entity, array $options = []): void
    {
        $this->tokenStorage->setToken(null);
        $this->flashBag->add('success', 'Votre compte a bien été supprimé');
    }
}
