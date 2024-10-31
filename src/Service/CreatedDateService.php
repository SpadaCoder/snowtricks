<?php

namespace App\Service;
use DateTimeInterface;

class CreatedDateService
{
    /**
     * Retourne la date de création actuelle.
     *
     * @return DateTimeInterface La date et l'heure actuelles sous forme d'instance de DateTimeInterface.
     */
    public function getCreatedDate(): DateTimeInterface
    {
          // Crée et retourne un nouvel objet DateTime correspondant à la date et l'heure actuelles.
        return new \DateTime();  
    }
}
