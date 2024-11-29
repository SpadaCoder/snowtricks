<?php

namespace App\Service;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Trick;
use App\Entity\User;

/**
 * Interface pour la gestion des images associées aux utilisateurs et aux Tricks.
 *
 * Cette interface définit les méthodes nécessaires pour télécharger, supprimer,
 * récupérer et remplacer des images, ainsi que pour gérer les images à la une.
 */
interface ImageServiceInterface
{
    /**
     * Télécharge une image, l'associe à un utilisateur et éventuellement à un trick.
     *
     * Cette méthode gère l'upload d'un fichier image, crée une entité Image, 
     * et associe l'image à un utilisateur et/ou un trick avant de la persister.
     *
     * @param UploadedFile $imageFile Le fichier image à télécharger.
     * @param User $user L'utilisateur associé à l'image.
     *
     * @return Image L'entité Image créée et persistée.
     */
    public function uploadImage(UploadedFile $imageFile, User $user): Image;

    /**
     * Supprime une image de la base de données et du serveur.
     *
     * Cette méthode supprime une entité Image de la base de données et supprime 
     * également le fichier correspondant du système de fichiers.
     *
     * @param Image $image L'image à supprimer.
     *
     * @return void
     */
    public function deleteImage(Image $image): void;

    /**
     * Supprime physiquement un fichier image du serveur.
     *
     * Cette méthode tente de supprimer le fichier d'image spécifié du système
     * de fichiers. Elle retourne un indicateur de succès.
     *
     * @param string $filePath Le chemin complet du fichier à supprimer.
     *
     * @return bool True si le fichier a été supprimé avec succès, False sinon.
     */
    public function deleteFile(string $filePath): bool;

    /**
     * Récupère la première image associée à un Trick.
     *
     * Cette méthode retourne la première image associée à un Trick donné,
     * ou null si aucune image n'est trouvée.
     *
     * @param Trick $trick Le trick pour lequel on souhaite récupérer l'image.
     *
     * @return Image|null L'entité Image correspondante, ou null si aucune image n'est trouvée.
     */
    public function getFirstImageByTrick(Trick $trick): ?Image;

    /**
     * Remplace une image existante par une nouvelle.
     *
     * Cette méthode supprime physiquement l'ancien fichier image, remplace les
     * données dans l'entité Image, et met à jour la base de données.
     *
     * @param Image $image L'entité Image existante à remplacer.
     * @param UploadedFile $newImageFile Le nouveau fichier image à associer.
     *
     * @return void
     */
    public function replaceImage(Image $image, UploadedFile $newImageFile): void;

    /**
     * Récupère l'URL de l'image à la une d'un Trick, ou une URL par défaut.
     *
     * Cette méthode vérifie si un Trick possède une image marquée comme à la une.
     * Si aucune image n'est trouvée, une URL d'image par défaut est retournée.
     *
     * @param Trick $trick Le Trick dont on souhaite obtenir l'image à la une.
     *
     * @return string L'URL de l'image à la une ou de l'image par défaut.
     */
    public function getFeaturedImageOrDefault(Trick $trick): string;
}
