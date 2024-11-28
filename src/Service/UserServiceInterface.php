<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Form\FormInterface;

interface UserServiceInterface
{
    /**
     * Traite le formulaire d'inscription.
     *
     * @param FormInterface $form Formulaire contenant les données de l'utilisateur
     * @param User          $user Instance de l'utilisateur à remplir et enregistrer
     */
    public function processRegistrationForm(FormInterface $form, User $user): void;
}