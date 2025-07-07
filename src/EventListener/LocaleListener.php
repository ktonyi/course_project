<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $locale = $request->get('_locale', $request->getSession()->get('_locale', 'ru'));

        if ($user = $request->getUser()) {
            $userEntity = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user]);
            if ($userEntity && $userEntity->getLanguage() !== $locale) {
                $userEntity->setLanguage($locale);
                $this->entityManager->persist($userEntity);
                $this->entityManager->flush();
            }
        }

        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);
    }
}