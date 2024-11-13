<?php

namespace App\Service;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService implements ImageServiceInterface
{
    private string $imagesDirectory;
    private EntityManagerInterface $entityManager;

    public function __construct(string $imagesDirectory, EntityManagerInterface $entityManager)
    {
        $this->imagesDirectory = $imagesDirectory;
        $this->entityManager = $entityManager;
    }

    public function uploadImage(UploadedFile $imageFile, $user): Image
    {

        // Générer un nom unique pour l'image
        $fileName = md5(uniqid()) . '.' . $imageFile->guessExtension();

        // Déplacer le fichier vers le répertoire de stockage
        $imageFile->move($this->imagesDirectory, $fileName);

        // Créer une entité Image et l'associer
        $image = new Image();
        $image->setName($fileName);
        $image->setUser($user);

        // Enregistrer l'image dans la base de données
        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return $image;
    }

    public function deleteImage(Image $image): void
    {
        // Supprimer physiquement le fichier du serveur
        $filePath = $this->imagesDirectory . '/' . $image->getName();
        $this->deleteFile($filePath);

        // Supprimer l'entrée dans la base de données
        $this->entityManager->remove($image);
        $this->entityManager->flush();
    }

    public function deleteFile(string $filePath): bool
    {
        // Vérifier si le fichier existe et le supprimer
        if (file_exists($filePath)) {
            unlink($filePath);
            return true;
        }
        return false;
    }

    public function replaceImage(Image $image, UploadedFile $newImageFile): void
    {
        // Vérifier si l'ancien fichier existe et le supprimer
        $oldFilePath = $this->imagesDirectory . '/' . $image->getName();
        if (file_exists($oldFilePath)) {
            $this->deleteFile($oldFilePath);  // Supprimer l'ancien fichier
        }

        // Uploader la nouvelle image
        $fileName = md5(uniqid()) . '.' . $newImageFile->guessExtension();
        $newImageFile->move($this->imagesDirectory, $fileName);

        // Mettre à jour l'entité Image avec le nouveau fichier
        $image->setName($fileName);

        // Persister l'image modifiée
        $this->entityManager->persist($image);

        // Enregistrer les modifications dans la base de données
        $this->entityManager->flush();
    }

    public function getFirstImageByTrick($trick): ?Image
    {
        // Récupérer la première image pour un trick
        return $this->entityManager->getRepository(Image::class)->findOneBy(['trick' => $trick], ['id' => 'ASC']);
    }

    public function getFeaturedImageOrDefault($trick): string
    {
        // Récupérer la première image pour un trick
        $featuredImage = $this->getFirstImageByTrick($trick);

        return $featuredImage ? 'uploads/tricks/' . $featuredImage->getName() : 'images/default-image.png';
    }
}
