<?php

namespace App\Service;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageServiceInterface
{
    /**
     * Upload une image et l'associe à un utilisateur et un trick.
     *
     * @param UploadedFile $imageFile Le fichier image à télécharger.
     * @param mixed $user L'utilisateur qui télécharge l'image.
     * @param mixed $trick Le trick auquel l'image sera associée.
     * @return Image L'entité Image créée et persistée.
     */
    public function uploadImage(UploadedFile $imageFile, $user): Image;

    /**
     * Supprime une image de la base de données.
     *
     * @param Image $image L'image à supprimer.
     */
    public function deleteImage(Image $image): void;

    /**
     * Supprime physiquement un fichier image du serveur.
     *
     * @param string $filePath Le chemin du fichier à supprimer.
     * @return bool True si le fichier a été supprimé, False sinon.
     */
    public function deleteFile(string $filePath): bool;

    /**
     * Récupère la première image associée à un trick.
     *
     * @param mixed $trick Le trick pour lequel on souhaite récupérer l'image.
     * @return Image|null Retourne l'entité Image correspondante ou null si aucune image n'est trouvée.
     */
    public function getFirstImageByTrick($trick): ?Image;


    // TODO
    public function replaceImage(Image $image, UploadedFile $newImageFile): void;

    // TODO
    public function getFeaturedImageOrDefault($trick): string;
}
