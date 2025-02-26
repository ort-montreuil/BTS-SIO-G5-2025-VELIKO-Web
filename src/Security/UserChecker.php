<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        // Vérifie si l'utilisateur est une instance de AppUser
        if (!$user instanceof AppUser) {
            return;
        }

        // Vérifie si l'utilisateur a vérifié son compte
        if (!$user->isVerified()) {
            // Le message personnalisé sera affiché dans le formulaire de connexion
            throw new CustomUserMessageAccountStatusException('Votre compte doit être vérifiée. Regardez vos mails');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Aucune vérification supplémentaire après l'authentification
        return;
    }
}