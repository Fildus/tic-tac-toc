<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RolesValidator extends ConstraintValidator
{
    private const profiles = [User::ROLE_USER, User::ROLE_ADMIN];

    public function validate($values, Constraint $constraint): void
    {
        if (empty($values)) {
            return;
        }

        if (!$constraint instanceof Roles) {
            return;
        }

        foreach ($values as $value) {
            if (!in_array($value, self::profiles, true)) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $value)
                    ->addViolation();
            }
        }
    }
}
