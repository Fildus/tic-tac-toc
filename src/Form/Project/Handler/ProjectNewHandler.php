<?php

declare(strict_types=1);

namespace App\Form\Project\Handler;

use App\Entity\Project;
use App\Form\Project\Type\ProjectNewType;
use App\Infrastructure\FormHandler\AbstractFormBuilder;

class ProjectNewHandler extends AbstractFormBuilder
{
    public function build(object $entity, array $options = []): array
    {
        if ($entity instanceof Project) {
            $entity->setUser($options['user']);
        }

        return parent::build($entity, $options);
    }

    public function entityName(): string
    {
        return Project::class;
    }

    public function entityType(): string
    {
        return ProjectNewType::class;
    }
}
