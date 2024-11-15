<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Video;

interface VideoServiceInterface
{
    public function addVideo(string $url, string $provider, Trick $trick): Video;
    public function create(string $url): Video;
    public function removeVideo(Video $video): Trick;
    public function getProviderFromUrl(string $url): ?string;

    /**
     * Convertit une URL de vidéo en une URL d'intégration (embed).
     *
     * @param string $url L'URL de la vidéo à convertir.
     * @return string|null L'URL d'intégration ou null si le format n'est pas reconnu.
     */
    public function convertToEmbedUrl(string $url): ?string;

// TO do

    public function editVideo(Video $video, string $newUrl): void;

}

