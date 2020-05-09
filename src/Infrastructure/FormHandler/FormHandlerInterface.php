<?php

namespace App\Infrastructure\FormHandler;

use Symfony\Component\Form\FormView;

interface FormHandlerInterface
{
    public function process(string $className, array $options = [], object $entity = null): self;

    public function isValid(): bool;

    public function getView(): FormView;

    public function getEntity(): object;
}
