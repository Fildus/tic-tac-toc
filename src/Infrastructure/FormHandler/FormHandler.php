<?php

namespace App\Infrastructure\FormHandler;

use _HumbugBoxe8a38a0636f4\Roave\BetterReflection\Reflection\Exception\ClassDoesNotExist;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class FormHandler implements FormHandlerInterface
{
    private ContainerInterface $container;
    private FormInterface $form;
    private object $entity;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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
            throw new ClassDoesNotExist();
        }

        $this->entity = $handler->build($entity ??= new $class(), $options);

        $this->form = $formFactory->create($handler->entityType(), $entity);
        $this->form->handleRequest($request);

        if ($this->isValid()) {
            $manager->getRepository($class);
            $manager->persist($this->entity);
            $manager->flush();
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
