<?php

declare(strict_types=1);

namespace App\Form\User\Handler;

use App\Entity\User;
use App\Form\User\Type\EditProfilAccountType;
use App\Infrastructure\FormHandler\AbstractFormBuilder;

class UserEditProfilHandler extends AbstractFormBuilder
{
    public function entityName(): string
    {
        return User::class;
    }

    public function entityType(): string
    {
        return EditProfilAccountType::class;
    }
}
