<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;
use App\Service\Slugger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    private Slugger $slugger;

    public function __construct(Slugger $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        // Récupérer l'utilisateur avec l'ID 1
        $user = $manager->getRepository('App\Entity\User')->find(1);

        // Les tricks.
        $tricks = [
            'Melon' => ['category' => 'Grabs', 'description' => 'Saisie de la carre backside de la planche entre les deux pieds avec la main avant.'],
            'Indy' => ['category' => 'Grabs', 'description' => 'Saisie de la carre frontside de la planche entre les deux pieds avec la main arrière.'],
            'Backflip' => ['category' => 'Flips', 'description' => 'Saut périlleux arrière.'],
            'Frontflip' => ['category' => 'Flips', 'description' => 'Saut périlleux avant.'],
            '180' => ['category' => 'Rotations', 'description' => 'Rotation de 180 degrés.'],
            '360' => ['category' => 'Rotations', 'description' => 'Rotation de 360 degrés, soit un tour complet.'],
            'Nose Slide' => ['category' => 'Slides', 'description' => 'Glisser sur une barre avec l’avant de la planche.'],
            'Tail Slide' => ['category' => 'Slides', 'description' => 'Glisser sur une barre avec l’arrière de la planche.'],
            'Method Air' => ['category' => 'Old School', 'description' => 'Saisie de la carre backside de la planche avec les genoux pliés.'],
            'Japan Air' => ['category' => 'Old School', 'description' => 'Saisie de l’avant de la planche avec la main avant et les genoux pliés.']
        ];

        // Créer et persister les tricks.
        foreach ($tricks as $name => $data) {
            $trick = new Trick();
            $trick->setName($name);
            $trick->setDescription($data['description']);

            // Générer un slug unique.
            $slug = $this->slugger->slugify($name);
            $trick->setSlug($slug);

            // Initialiser les champs created et modified
            $trick->setCreated(new \DateTime());
            $trick->setModified(new \DateTime());

            // Récupérer la catégorie correspondante créée dans la fixture TrickCategoryFixtures.
            $category = $this->getReference('category_' . $data['category']);
            $trick->setCategory($category);

            // Associer le trick à l'utilisateur
            $trick->setUser($user);

            $manager->persist($trick);
        }

        // Sauvegarder toutes les entités en base de données.
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TrickCategoryFixtures::class, // Dépendance à TrickCategoryFixtures pour que les catégories soient déjà présentes
        ];
    }
}
