<?php

namespace App\Service;

use App\Entity\User;

interface EmailServiceInterface
{
    /**
     * Envoie un email de confirmation.
     *
     * @param User   $user      L'utilisateur cible
     * @param string $routeName Nom de la route pour la confirmation
     * @param string $fromEmail Adresse email de l'expéditeur
     */
    public function sendEmailConfirmation(User $user, string $routeName, string $fromEmail): void;
}