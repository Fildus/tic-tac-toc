<?php

declare(strict_types=1);

namespace App\Form\Project\Handler;

use App\Entity\Project;
use App\Infrastructure\DeleteHandler\AbstractDeleteBuilder;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ProjectDeleteHandler extends AbstractDeleteBuilder
{
    /** @required */
    public FlashBagInterface $flashBag;

    public function build(object $entity, array $options = []): array
    {
        return parent::build($entity, $options);
    }

    public function onSuccess(object $entity, array $options = []): void
    {
        if (!$entity instanceof Project) {
            return;
        }

        $this->flashBag->add('success', 'Le project <strong>'.$entity->getTitle().'</strong> a bien été supprimé');
    }
}
