<?php

declare(strict_types=1);

namespace App\Form\Project\Handler;

use App\Entity\Project;
use App\Form\Project\Type\ProjectEditType;
use App\Infrastructure\FormHandler\AbstractFormBuilder;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ProjectEditHandler extends AbstractFormBuilder
{
    /** @required */
    public FlashBagInterface $flashBag;

    public function entityName(): string
    {
        return Project::class;
    }

    public function entityType(): string
    {
        return ProjectEditType::class;
    }

    public function onSuccess(object $entity, array $options = []): void
    {
        if (!$entity instanceof Project) {
            return;
        }

        $this->flashBag->add('success', 'Le project <strong>'.$entity->getTitle().'</strong> a bien été édité');
    }
}
