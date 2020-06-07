<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Services\EncodePassword;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;

class UserSubscriber implements EventSubscriberInterface
{
    /** @required */
    public EncodePassword $encodePassword;

    public function postSubmit(FormEvent $event): void
    {
        if ($event->getData() instanceof User) {
            /** @var User $user */
            $user = $event->getData();
            $this->encodePassword->encode($user);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'form.post_submit' => 'postSubmit',
        ];
    }
}
