<?php

declare(strict_types=1);

namespace App\Form\Project\Handler;

use App\Entity\Project;
use App\Form\Project\Type\ProjectNewType;
use App\Infrastructure\FormHandler\AbstractFormBuilder;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ProjectNewHandler extends AbstractFormBuilder
{
    private FlashBagInterface $flashBag;
    private RouterInterface $router;

    public function __construct(FlashBagInterface $flashBag, RouterInterface $router)
    {
        $this->flashBag = $flashBag;
        $this->router = $router;
    }

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

    public function onSuccess(object $entity, array $options = []): void
    {
        if (!$entity instanceof Project) {
            return;
        }

        $url = $this->router->generate('project_edit', [
            'id' => $entity->getId(),
        ]);

        $this->flashBag->add('success', [
            'header' => 'Projet <strong>'.$entity.'</strong>',
            'body' => 'bravo vous venez de créer un nouveau projet',
            'footer' => '<a href="'.$url.'">Éditez votre projet</a>',
        ]);
    }
}
