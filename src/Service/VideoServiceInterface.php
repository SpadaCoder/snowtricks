<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Video;

/**
 * Interface VideoServiceInterface
 *
 * Cette interface définit les fonctionnalités nécessaires pour la gestion des vidéos associées aux tricks.
 */
interface VideoServiceInterface
{
    /**
     * Ajoute une nouvelle vidéo à un Trick.
     *
     * @param string $url L'URL de la vidéo (YouTube, Vimeo, etc.).
     * @param string $provider Le fournisseur de la vidéo (ex. : "YouTube", "Vimeo").
     * @param Trick $trick L'entité Trick à laquelle la vidéo doit être associée.
     *
     * @return Video L'entité Video nouvellement créée.
     */
    public function addVideo(string $url, string $provider, Trick $trick): Video;

    /**
     * Crée une entité Video sans la persister.
     *
     * @param string $url L'URL de la vidéo à créer.
     *
     * @return Video Une entité Video configurée mais non enregistrée en base de données.
     */
    public function create(string $url): Video;

    /**
     * Supprime une vidéo associée à un Trick.
     *
     * @param Video $video L'entité Video à supprimer.
     *
     * @return Trick Le Trick associé à la vidéo supprimée.
     */
    public function removeVideo(Video $video): Trick;

    /**
     * Récupère le fournisseur de la vidéo à partir de l'URL.
     *
     * @param string $url L'URL de la vidéo.
     *
     * @return string|null Le nom du fournisseur (ex. : "YouTube", "Vimeo"), ou null si inconnu.
     */
    public function getProviderFromUrl(string $url): ?string;

    /**
     * Convertit une URL de vidéo en URL d'intégration (embed).
     *
     * @param string $url L'URL de la vidéo à convertir.
     *
     * @return string|null L'URL d'intégration (embed) ou null si le format n'est pas reconnu.
     */
    public function convertToEmbedUrl(string $url): ?string;

    /**
     * Modifie l'URL d'une vidéo existante.
     *
     * @param Video $video L'entité Video à modifier.
     * @param string $newUrl La nouvelle URL de la vidéo.
     *
     * @throws \InvalidArgumentException Si l'URL n'est pas reconnue comme valide.
     */
    public function editVideo(Video $video, string $newUrl): void;
}

