<?php

declare(strict_types=1);

namespace App\Form\Project\Handler;

use App\Infrastructure\DeleteHandler\AbstractDeleteBuilder;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ProjectDeleteHandler extends AbstractDeleteBuilder
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function build(object $entity, array $options = []): array
    {
        return parent::build($entity, $options);
    }

    public function onSuccess(object $entity, array $options = []): void
    {
        $this->flashBag->add('notice', 'Le project a bien été supprimé');
    }
}
