<?php

namespace App\Infrastructure\FormHandler;

abstract class AbstractFormBuilder
{
    public function build(object $entity, array $options = []): object
    {
        return $entity;
    }

    abstract public function entityName(): string;

    abstract public function entityType(): string;
}
