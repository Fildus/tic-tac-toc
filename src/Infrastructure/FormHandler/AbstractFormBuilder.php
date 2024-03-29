<?php

declare(strict_types=1);

namespace App\Infrastructure\FormHandler;

abstract class AbstractFormBuilder
{
    public function build(object $entity, array $options = []): array
    {
        return [$entity, $options];
    }

    public function onSuccess(object $entity, array $options = []): void
    {
    }

    abstract public function entityName(): string;

    abstract public function entityType(): string;
}
