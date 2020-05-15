<?php

declare(strict_types=1);

namespace App\Form\User\Handler;

use App\Entity\User;
use App\Form\User\Type\UpdatePasswordType;
use App\Infrastructure\FormHandler\AbstractFormBuilder;

class UserUpdatePasswordHandler extends AbstractFormBuilder
{
    public function entityName(): string
    {
        return User::class;
    }

    public function entityType(): string
    {
        return UpdatePasswordType::class;
    }
}
