<?php

declare(strict_types=1);

namespace App\Form\User\Handler;

use App\Entity\User;
use App\Form\User\Type\CreateAccountType;
use App\Infrastructure\FormHandler\AbstractFormBuilder;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class UserRegistrationHandler extends AbstractFormBuilder
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function entityName(): string
    {
        return User::class;
    }

    public function entityType(): string
    {
        return CreateAccountType::class;
    }

    public function onSuccess(object $entity, array $options = []): void
    {
        if (!$entity instanceof User) {
            return;
        }

        $this->flashBag->add('success', [
            'header' => '<strong>'.$entity->getUsername().'</strong>',
            'body' => 'bravo votre compte vient d\'être créé!',
        ]);
    }
}
