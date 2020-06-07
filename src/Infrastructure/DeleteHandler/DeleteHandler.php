<?php

declare(strict_types=1);

namespace App\Infrastructure\DeleteHandler;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class DeleteHandler implements DeleteHandlerInterface
{
    /** @required */
    public ContainerInterface $container;
    private bool $isValid = false;

    /**
     * @return $this
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function process(object $entity = null, ?string $className = null, array $options = []): self
    {
        /* @var AbstractDeleteBuilder $handler */

        $handler = $this->container->has((string) $className) ?
            $this->container->get((string) $className) : null;

        [$entity, $options] = $handler->build($entity, $options);

        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();

        /** @var CsrfTokenManager $csrfTokenManager */
        $csrfTokenManager = $this->container->get('security.csrf.token_manager');

        /** @var EntityManager $manager */
        $manager = $this->container->get('doctrine')->getManager();

        $id = $request->attributes->get('id');
        $token = new CsrfToken('delete'.$id, $request->request->get('_token'));

        if ($csrfTokenManager->isTokenValid($token)) {
            $manager->remove($entity);
            $manager->flush();
            $this->isValid = true;

            $handler->onSuccess($entity, $options);
        }

        return $this;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }
}
