<?php

declare(strict_types=1);

namespace App\Infrastructure\FormHandler;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FormHandler implements FormHandlerInterface
{
    /** @required */
    public ContainerInterface $container;
    private FormInterface $form;
    private object $entity;

    /**
     * @return $this
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function process(string $className, array $options = [], object $entity = null): self
    {
        /** @var AbstractFormBuilder $handler */
        $handler = $this->container->get($className);

        /** @var FormFactory $formFactory */
        $formFactory = $this->container->get('form.factory');

        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();

        /** @var EntityManager $manager */
        $manager = $this->container->get('doctrine')->getManager();

        $class = $handler->entityName();

        if (!class_exists($class)) {
            throw new NotFoundHttpException();
        }

        [$this->entity, $options] = $handler->build($entity ??= new $class(), $options);

        $this->form = $formFactory->create($handler->entityType(), $entity);
        $this->form->handleRequest($request);

        if ($this->isValid()) {
            $manager->getRepository($class);
            $manager->persist($this->entity);
            $manager->flush();

            $handler->onSuccess($entity, $options);
        }

        return $this;
    }

    public function isValid(): bool
    {
        return $this->form->isSubmitted() && $this->form->isValid();
    }

    public function getView(): FormView
    {
        return $this->form->createView();
    }

    public function getEntity(): object
    {
        return $this->entity;
    }
}
