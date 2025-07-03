<?php

namespace App\Security;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class BlockedUserChecker implements UserCheckerInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            $this->logger->debug('User is not instance of App\Entity\User', ['user' => get_class($user)]);
            return;
        }

        $this->logger->debug('Checking user block status', ['email' => $user->getEmail(), 'isBlocked' => $user->isBlocked()]);

        if ($user->isBlocked()) {
            throw new CustomUserMessageAuthenticationException('Your account is blocked.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        
    }
}