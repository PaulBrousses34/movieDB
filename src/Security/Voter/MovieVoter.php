<?php

namespace App\Security\Voter;

use App\Entity\Movie;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MovieVoter extends Voter
{
    // La méthode supports retourne un booléen indiquant si la classe de Voter
    // prend en charge le droit demandé pour l'objet demandé
    // "démandé" = dans le contrôleur, quand on fait $this->denyAccessUnlessGranted(le droit, l'objet)
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['EDIT', 'VIEW', 'DELETE'])
            && $subject instanceof Movie;
    }

    // Si supports retourne true, la méthode voteOnAttribute est exécutée
    // Si elle retourne false, on interdit l'accès (deny access)
    // Si elle retourne true, on autorise les droits (it is granted)
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Voici comment on récupère l'utilisateur
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'EDIT':
            case 'VIEW':
            case 'DELETE':
                if ($subject->getAuthor() == $user) {
                    return true;
                }
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;
        }

        return false;
    }
}
