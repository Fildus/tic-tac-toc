<?php

declare(strict_types=1);

namespace App\Form\User\Handler;

use App\Infrastructure\DeleteHandler\AbstractDeleteBuilder;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserDeleteHandler extends AbstractDeleteBuilder
{
    private FlashBagInterface $flashBag;
    private TokenStorageInterface $tokenStorage;

    public function __construct(FlashBagInterface $flashBag, TokenStorageInterface $tokenStorage)
    {
        $this->flashBag = $flashBag;
        $this->tokenStorage = $tokenStorage;
    }

    public function onSuccess(object $entity, array $options = []): void
    {
        $this->tokenStorage->setToken(null);
        $this->flashBag->add('success', 'Votre compte a bien été supprimé');
    }
}
