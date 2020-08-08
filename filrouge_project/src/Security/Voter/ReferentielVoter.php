<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ReferentielVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['REF_EDIT', 'REF_VIEW'])
            && $subject instanceof \App\Entity\Referentiel;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'REF_EDIT':
                break;
            case 'REF_VIEW':
                break;
        }

        return false;
    }
}
