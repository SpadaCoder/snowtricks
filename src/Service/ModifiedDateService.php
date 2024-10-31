<?php

namespace App\Service;
use DateTimeInterface;

/**
 * Service pour obtenir la date de modification.
 * 
 * Cette classe fournit une méthode qui retourne la date et l'heure actuelles,
 * représentant la dernière modification d'un élément.
 */
class ModifiedDateService
{
    /**
     * Retourne la date de modification actuelle.
     *
     * @return DateTimeInterface La date et l'heure actuelles sous forme d'instance de DateTimeInterface.
     */
    public function getModifiedDate(): DateTimeInterface
    {
        // Crée et retourne un nouvel objet DateTime correspondant à la date et l'heure actuelles.
        return new \DateTime();  
    }
}
