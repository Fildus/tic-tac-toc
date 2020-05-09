<?php

namespace App\Form\Project\Handler;

use App\Entity\Project;
use App\Form\Project\Type\ProjectNewType;
use App\Infrastructure\FormHandler\AbstractFormBuilder;

class ProjectEditHandler extends AbstractFormBuilder
{
    public function entityName(): string
    {
        return Project::class;
    }

    public function entityType(): string
    {
        return ProjectNewType::class;
    }
}
