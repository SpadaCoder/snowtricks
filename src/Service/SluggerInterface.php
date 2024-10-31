<?php

namespace App\Service;

/**
 * Interface pour le service de génération de slugs.
 * 
 * Cette interface définit une méthode pour convertir une chaîne de texte en un slug,
 * un format souvent utilisé pour les URLs lisibles et compatibles.
 */
interface SluggerInterface
{
     /**
     * Convertit une chaîne de texte en slug.
     * 
     * Cette méthode prend en paramètre un texte et le convertit en un slug,
     * c'est-à-dire une chaîne formatée pour être utilisée dans les URLs.
     *
     * @param string $text Le texte à convertir en slug.
     * @return string Le slug généré à partir du texte.
     */
    public function slugify(string $text): string;
}
