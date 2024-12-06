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

    /**
     * Définit le mot de passe haché d'un utilisateur.
     *
     * @param User $user L'utilisateur dont le mot de passe doit être modifié
     * @param string $plainPassword Le nouveau mot de passe en texte clair
     */
    public function setUserPassword(User $user, string $plainPassword): void;

    /**
     * Marque un utilisateur comme vérifié.
     *
     * Cette méthode met à jour le champ correspondant dans l'entité utilisateur
     * pour indiquer que l'utilisateur a confirmé son email.
     * 
     * @param User $user L'utilisateur à vérifier.
     */
    public function verifyUser(User $user): void;

    /**
     * Persiste et enregistre un utilisateur dans la base de données.
     *
     * @param User $user L'entité User à persister dans la base de données.
     */
    public function save(User $user): void;
}