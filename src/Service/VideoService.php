<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;

class VideoService implements VideoServiceInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Ajoute une nouvelle vidéo
    public function addVideo(string $url, string $provider, Trick $trick): Video
    {
        // Convertir l'URL de la vidéo en URL d'intégration (embed)
        $embedUrl = $this->convertToEmbedUrl($url);

        // Créer une nouvelle instance de Video
        $video = new Video();
        $video->setName($embedUrl);
        $video->setProvider($this->getProviderFromUrl($url));
        // Associer la vidéo au Trick
        $video->setTrick($trick);
        dd($video);

        $this->entityManager->persist($video);
        $this->entityManager->flush();

        return $video;

    }

    public function create(string $url): Video
    {
        // Convertir l'URL de la vidéo en URL d'intégration (embed)
        $embedUrl = $this->convertToEmbedUrl($url);

        // Créer une nouvelle instance de Video
        $video = new Video();
        $video->setName($embedUrl);
        $video->setProvider($this->getProviderFromUrl($url));

        return $video;
    }

    // Supprime une vidéo
    public function removeVideo(Video $video): void
    {
        $this->entityManager->remove($video);
        $this->entityManager->flush();
    }

    // Récupère le fournisseur de la vidéo à partir de l'URL
    public function getProviderFromUrl(string $url): ?string
    {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'YouTube';
        } elseif (strpos($url, 'vimeo.com') !== false) {
            return 'Vimeo';
        }

        return null; // Provider inconnu
    }


    /**
     * Convertit une URL de vidéo en une URL d'intégration (embed).
     *
     * @param string $url L'URL de la vidéo à convertir.
     * @return string|null L'URL d'intégration ou null si le format n'est pas reconnu.
     */
    public function convertToEmbedUrl(string $url): ?string
    {
        // Vérification pour YouTube
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return preg_replace(
                "/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i",
                "https://www.youtube.com/embed/$1",
                $url
            );
        }

        // Vérification pour Vimeo
        if (strpos($url, 'vimeo.com') !== false) {
            return preg_replace(
                "/(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(\d+)/i",
                "https://player.vimeo.com/video/$1",
                $url
            );
        }

        // Vérification pour Dailymotion
        if (strpos($url, 'dailymotion.com') !== false) {
            return preg_replace(
                "/(?:https?:\/\/)?(?:www\.)?dailymotion\.com\/video\/([a-zA-Z0-9]+)/i",
                "https://www.dailymotion.com/embed/video/$1",
                $url
            );
        }

        // Retourne null si aucun format n'est reconnu
        return null;
    }

}
