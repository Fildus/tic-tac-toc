<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Roles extends Constraint
{
    public string $message = 'Le rôle "{{ value }}" n\'existe pas';
}
