<?php

namespace App\Security\Voter;

use App\Security\Entity\OwnedByInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OwnerVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['DELETE', 'PATCH', 'PUT'], true)
            && $subject instanceof OwnedByInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof UserInterface) {
            return false;
        }

        \assert($subject instanceof OwnedByInterface);

        return $subject->getOwnedBy() === $currentUser;
    }
}
