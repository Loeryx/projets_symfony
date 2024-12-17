<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, entity: User::class)]
readonly class UserHashPasswordSubscriber
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher){}

    public function prePersist(User $user): void {
        $this->hashPassword($user);
    }

    public function preUpdate(User $user): void {
        $this->hashPassword($user);
    }

    private function hashPassword(User $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if (!$plainPassword) {
            return;
        }

        $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($encodedPassword);
        $user->eraseCredentials();
    }
}