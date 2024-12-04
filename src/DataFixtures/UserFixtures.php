<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('default_user');
        $user->setLastname('default_user');
        $user->setEmail('default_user@example.com');
        $user->setCreated(new \DateTime());
        $user->setModified(new \DateTime());
        $user->setVerified(true);
        $user->setProfilPicture('public\images\default-profil.png');

        // Hachage du mot de passe
        $password = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($password);

        $manager->persist($user);

        // Ajouter une référence pour l'utiliser dans d'autres fixtures
        $this->addReference('default_user', $user);

        $manager->flush();
    }
}
