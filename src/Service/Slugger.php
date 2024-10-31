<?php

namespace App\Service;

use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Service pour générer des slugs à partir de chaînes de caractères.
 * 
 * Cette classe utilise l'AsciiSlugger de Symfony pour convertir du texte en un format "slug",
 * c'est-à-dire une version lisible et compatible avec les URLs.
 */
class Slugger implements SluggerInterface
{

    /**
     * @var AsciiSlugger $slugger Instance du slugger utilisé pour la conversion.
     */
    private AsciiSlugger $slugger;

    /**
     * Constructeur de la classe Slugger.
     * 
     * Initialise l'AsciiSlugger pour gérer la conversion de texte en slug.
     */
    public function __construct()
    {
        $this->slugger = new AsciiSlugger();
    }

    /**
     * Convertit une chaîne de texte en slug.
     * 
     * Cette méthode prend une chaîne de texte en entrée, la convertit en un slug
     * (texte en minuscules, séparé par des tirets) et la renvoie.
     * 
     * @param string $text Le texte à convertir en slug.
     * @return string Le slug résultant.
     */
    public function slugify(string $text): string
    {
        return $this->slugger->slug($text)->lower();
    }
}
