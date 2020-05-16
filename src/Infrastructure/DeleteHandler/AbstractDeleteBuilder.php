<?php

declare(strict_types=1);

namespace App\Infrastructure\DeleteHandler;

abstract class AbstractDeleteBuilder
{
    public function build(object $entity, array $options = []): array
    {
        return [$entity, $options];
    }

    public function onSuccess(object $entity, array $options = []): void
    {
    }
}
