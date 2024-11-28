<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Image;

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
     * Cette méthode prend en paramètre une entité Trick, un formulaire contenant
     * les données à traiter, ainsi que l'utilisateur créant le Trick. Les images
     * fournies dans le formulaire seront associées au Trick avant sa persistance.
     *
     * @param Trick $trick L'entité Trick à créer.
     * @param mixed $form Les données du formulaire à traiter.
     * @param User $user L'utilisateur créateur du Trick.
     *
     * @return void
     */
    public function create(Trick $trick, mixed $form, User $user): void;

    /**
     * Met à jour un Trick existant et gère ses images associées.
     *
     * Cette méthode prend en charge la mise à jour des propriétés d'un Trick,
     * la gestion des images fournies dans le formulaire, et la persistance des
     * changements dans la base de données.
     *
     * @param Trick $trick L'entité Trick à mettre à jour.
     * @param mixed $form Les données du formulaire mises à jour.
     * @param User $user L'utilisateur effectuant la modification.
     *
     * @return void
     */
    public function update(Trick $trick, mixed $form, User $user): void;

    /**
     * Supprime un Trick et ses images associées.
     *
     * Cette méthode s'assure de la suppression complète d'un Trick, y compris
     * toutes ses images associées, de la base de données.
     *
     * @param Trick $trick L'entité Trick à supprimer.
     *
     * @return void
     */
    public function delete(Trick $trick): void;

    /**
     * Récupère l'URL de l'image à la une associée à un Trick.
     *
     * Si le Trick ne possède pas d'image à la une, une URL d'image par défaut
     * sera retournée.
     *
     * @param Trick $trick L'objet Trick dont on souhaite obtenir l'image à la une.
     *
     * @return string L'URL de l'image à la une ou de l'image par défaut.
     */
    public function getFeaturedImageOrDefault(Trick $trick): string;

    /**
     * Sauvegarde ou met à jour un Trick dans la base de données.
     *
     * Cette méthode gère à la fois la création et la mise à jour d'un Trick
     * en fonction de son état (nouvelle entité ou existante).
     *
     * @param Trick $trick L'entité Trick à sauvegarder.
     *
     * @return void
     */
    public function save(Trick $trick): void;
}
