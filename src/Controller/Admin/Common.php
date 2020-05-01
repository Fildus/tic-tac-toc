<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

trait Common
{
    public function isRouteName(string $routeName): bool
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->get('request_stack');

        return $routeName === $requestStack->getCurrentRequest()->get('route_name', 'undefined');
    }

    public function isCrudAction(string $route): bool
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->get('request_stack');

        if (!$requestStack instanceof RequestStack) {
            return false;
        }

        /** @var AdminContext $adminContext */
        $adminContext = $requestStack->getCurrentRequest()->get('easyadmin_context');

        if (!$adminContext instanceof AdminContext) {
            return false;
        }

        /** @var string|null $routeName */
        $routeName = $adminContext->getRequest()->query->get('route_name');

        if (!$routeName) {
            return false;
        }

        return null !== $routeName && $route === $routeName;
    }

    public function redirectToCrud(AdminContext $context, string $actionName): Response
    {
        $parameterBag = $context->getRequest()->query;

        $entity = $context->getEntity()->getInstance();

        return $this->redirectToRoute('admin', [
            'crudAction' => $actionName,
            'crudController' => $parameterBag->get('crudController'),
            'route_name' => $parameterBag->get('crudAction'),
            'entityId' => $entity->getId(),
        ]);
    }
}
