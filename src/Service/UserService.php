<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Form\FormInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private string $profilDirectory
    ) {}

    public function processRegistrationForm(FormInterface $form, User $user): void
    {
        // Récupération des données du formulaire
        $plainPassword = $form->get('plainPassword')->getData();
        /** @var UploadedFile|null $profilePicture */
        $profilPicture = $form->get('profilPicture')->getData();

        // Traitement des données
        $this->setUserPassword($user, $plainPassword);
        $this->handleProfilPicture($user, $profilPicture);

        // Ajout des valeurs par défaut
        $user->setCreated(new \DateTime());
        $user->setModified(new \DateTime());
        $user->setRoles(['ROLE_USER']);

        // Enregistrement dans la base de données
        $this->save($user);
    }

    public function setUserPassword(User $user, string $plainPassword): void
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $plainPassword)
        );
    }

    private function handleProfilPicture(User $user, ?UploadedFile $profilPicture): void
    {
        if (!$profilPicture) {
            return;
        }

        $newFilename = uniqid() . '.' . $profilPicture->guessExtension();

        try {
            $profilPicture->move($this->profilDirectory, $newFilename);
            $user->setProfilPicture($newFilename);
        } catch (FileException $e) {
            throw new \RuntimeException('Erreur lors du téléchargement de l\'image de profil.', 0, $e);
        }
    }

    public function verifyUser(User $user): void
    {
        $user->setVerified(true);

        $this->save($user);
    }

    // Fonction save pour persister et flusher l'entité User
    public function save(User $user): void
    {
        // Persist de l'entité User
        $this->entityManager->persist($user);

        // Flusher pour enregistrer dans la base de données
        $this->entityManager->flush();
    }
}
