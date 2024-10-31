<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Common\Collections\Collection;

/**
 * Interface pour la gestion des Tricks.
 * 
 * Cette interface définit les méthodes nécessaires pour créer, mettre à jour, 
 * sauvegarder et supprimer des entités Trick ainsi que gérer leurs images associées.
 */
interface TrickServiceInterface
{
    /**
     * Crée un nouveau Trick et gère ses images associées.
     * 
     * Cette méthode prend en paramètre un Trick ainsi qu'un tableau de fichiers d'images
     * et s'assure de leur association avant de persister l'entité.
     *
     * @param Trick $trick L'entité Trick à créer.
     * @param $form Les données du formulaire.
     */
    public function create(Trick $trick, $form, User $user): void;

    /**
     * Met à jour un Trick existant et gère ses images associées.
     * 
     * Cette méthode met à jour le Trick et ses images dans la base de données.
     *
     * @param Trick $trick L'entité Trick à mettre à jour.
     * @param array $imageFiles Les fichiers d'images à associer au Trick.
     */
    public function update(Trick $trick, $form, User $user): void;

    /**
     * Supprime un Trick et ses images associées.
     * 
     * Cette méthode supprime le Trick de la base de données ainsi que les images associées.
     *
     * @param Trick $trick L'entité Trick à supprimer.
     */
    public function delete(Trick $trick): void;

    /**
     * Récupère l'image à la une associée à un trick.
     *
     * @param Trick $trick L'objet Trick dont on souhaite obtenir l'image à la une.
     * @return Image|null L'image à la une si elle existe, sinon null.
     */
    public function getFeaturedImageOrDefault(Trick $trick): string;

        /**
     * Sauvegarde ou met à jour un Trick dans la base de données.
     * 
     * Cette méthode persiste l'entité Trick dans la base de données.
     *
     * @param Trick $trick L'entité Trick à sauvegarder.
     */
    public function save(Trick $trick);
}
