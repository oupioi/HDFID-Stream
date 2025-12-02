<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginFlashSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onInteractiveLogin',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        $user = $event->getAuthenticationToken()->getUser();
        $displayName = method_exists($user, 'getFirstName') && $user->getFirstName()
            ? $user->getFirstName()
            : $user->getUserIdentifier();

        $session->getFlashBag()->add('success', sprintf('Quoi de neuf %s ? Tu as besoin de changer quelque chose ici ?', $displayName));
    }
}
