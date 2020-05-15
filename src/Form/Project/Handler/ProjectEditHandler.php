<?php

declare(strict_types=1);

namespace App\Form\Project\Handler;

use App\Entity\Project;
use App\Form\Project\Type\ProjectEditType;
use App\Infrastructure\FormHandler\AbstractFormBuilder;

class ProjectEditHandler extends AbstractFormBuilder
{
    public function entityName(): string
    {
        return Project::class;
    }

    public function entityType(): string
    {
        return ProjectEditType::class;
    }
}
