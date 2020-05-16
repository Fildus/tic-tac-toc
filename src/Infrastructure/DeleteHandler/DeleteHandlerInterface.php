<?php

declare(strict_types=1);

namespace App\Infrastructure\DeleteHandler;

interface DeleteHandlerInterface
{
    public function process(object $entity = null, ?string $className = null, array $options = []): DeleteHandler;
}
