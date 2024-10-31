<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrickCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Les catégories.
        $categories = [
            'Grabs' => 'Saisir la planche avec la main.',
            'Rotations' => 'Tourner sur soi-même en l’air.',
            'Flips' => 'Sauts périlleux.',
            'Slides' => 'Glisser sur une barre de slide avec la planche.',
            'Old School' => 'Tricks traditionnels des débuts du snowboard freestyle.'
        ];
        
        // Créer et persister les catégories.
        foreach ($categories as $name => $description) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);

        // Ajouter une référence pour que TrickFixtures puisse y accéder.
        $this->addReference('category_'.$name, $category);
    }

        $manager->flush();
    }
}
