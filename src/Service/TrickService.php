<?php

namespace App\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\User;
use App\Service\TrickServiceInterface;
use App\Service\ImageServiceInterface;
use App\Service\VideoServiceInterface;


/**
 * Service pour la gestion des entités Trick.
 * 
 * Ce service fournit des méthodes pour créer, mettre à jour, sauvegarder et supprimer
 * des entités Trick, ainsi que pour gérer leurs images associées.
 */
class TrickService implements TrickServiceInterface
{
    /**
     * Constructeur de TrickService.
     * 
     * @param EntityManagerInterface $entityManager Gère la persistance des entités.
     * @param ImageServiceInterface $imageService Gère l'upload et la suppression des images associées aux tricks.
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ImageServiceInterface $imageService,
        private readonly SluggerInterface $slugger,
        private readonly CreatedDateService $createdDateService,
        private readonly ModifiedDateService $modifiedDateService,
        private readonly VideoServiceInterface $videoService
    ) {
    }

    /**
     * Sauvegarde ou met à jour un Trick dans la base de données.
     * 
     * Cette méthode persiste l'entité Trick dans la base de données.
     * 
     * @param Trick $trick L'entité Trick à sauvegarder.
     */
    public function save(Trick $trick)
    {
        $this->entityManager->persist($trick);
        $this->entityManager->flush();
    }

    /**
     * Crée un nouveau Trick et gère ses images associées.
     * 
     * Cette méthode upload les fichiers d'image associés et persiste le Trick dans la base de données.
     * 
     * @param Trick $trick L'entité Trick à créer.
     * @param $form Les données du formulaire.
     */
    public function create(Trick $trick, $form, User $user): void
    {
        // Assigner la date de création avec le service
        $trick->setCreated($this->createdDateService->getCreatedDate());

        // Assigner la date de modification avec le service
        $trick->setModified($this->modifiedDateService->getModifiedDate());

        // Générer le slug avec le SluggerService
        $slug = $this->slugger->slugify($trick->getName());
        $trick->setSlug($slug);

        // Associer l'utilisateur au Trick
        $trick->setUser($user);

        // Upload des images et association au Trick.
        $imageFiles = $form->get('images')->getData();
        foreach ($imageFiles as $imageFile) {
            if ($imageFile) {
                $image = $this->imageService->uploadImage($imageFile, $user);

                // Ajout de l'image uniquement si elle est bien créée
                if ($image instanceof Image) {
                    $trick->addImage($image);
                }
            }
        }
        // Récupération des Urls et association au Trick.
        $videoUrls = $form->get('videos')->getData();
        foreach ($videoUrls as $videoUrl) {
            if (null !== $videoUrl) {
                $video = $this->videoService->create($videoUrl);

                // Ajout de l'image uniquement si elle est bien créée
                $trick->addVideo($video);

            }
        }


        // Sauvegarde le Trick dans la base de données.
        $this->save($trick);
    }

    /**
     * Met à jour un Trick existant et gère ses images associées.
     * 
     * Cette méthode upload les nouveaux fichiers d'image associés et met à jour le Trick dans la base de données.
     * 
     * @param Trick $trick L'entité Trick à mettre à jour.
     * @param array $form Les données du formulaire.
     */
    public function update(Trick $trick, $form, User $user): void
    {
        // Mise à jour de la date de modification
        $trick->setModified($this->modifiedDateService->getModifiedDate());

        // Upload des nouvelles images et association au Trick.
        $imageFiles = $form->get('images')->getData();
        foreach ($imageFiles as $imageFile) {
            if ($imageFile) {
                $image=$this->imageService->uploadImage($imageFile, $user);

                // Ajout de l'image uniquement si elle est bien créée
                if ($image instanceof Image) {
                    $trick->addImage($image);
                }
            }
        }

        // Récupérer et définir l'image à la une (si elle n'est pas encore définie)
        $featuredImage = $this->imageService->getFeaturedImageOrDefault($trick);

        // Gestion des vidéos associées au Trick.
        $videoUrls = $form->get('videos')->getData();

        foreach ($videoUrls as $videoUrl) {
            if (null !== $videoUrl) {
                $video = $this->videoService->create($videoUrl);

                // Associer la vidéo au Trick
                $trick->addVideo($video);

            }

            // Met à jour le Trick dans la base de données.
            $this->save($trick);
        }
    }

    /**
     * Supprime un Trick et ses images associées.
     * 
     * Cette méthode supprime d'abord les fichiers d'images associés au Trick,
     * puis supprime l'entité Trick de la base de données.
     * 
     * @param Trick $trick L'entité Trick à supprimer.
     */
    public function delete(Trick $trick): void
    {
        // Supprimer chaque image associée au Trick.
        foreach ($trick->getImages() as $image) {
            $this->imageService->deleteImage($image);
        }

        // Supprimer le Trick de la base de données.
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
    }

    /**
     * Récupère l'image à la une associée à un trick.
     *
     * @param Trick $trick L'objet Trick dont on souhaite obtenir l'image à la une.
     * @return Image|null L'image à la une si elle existe, sinon null.
     */
    public function getFeaturedImageOrDefault(Trick $trick): string
    {
        return $this->imageService->getFeaturedImageOrDefault($trick);
    }


}
