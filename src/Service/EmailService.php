<?php

namespace App\Service;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class EmailService implements EmailServiceInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private EmailVerifier $emailVerifier;

    public function __construct(UrlGeneratorInterface $urlGenerator, EmailVerifier $emailVerifier) 
    {
        $this->urlGenerator = $urlGenerator;
        $this->emailVerifier = $emailVerifier;
    }

    public function sendEmailConfirmation(User $user, string $routeName, string $fromEmail): void
    {
        // Préparer l'email avec un template et les données nécessaires
        $email = (new TemplatedEmail())
            ->from(new Address($fromEmail, 'Snowtricks Mail Bot'))
            ->to($user->getEmail())
            ->subject('Merci de confirmer votre Email')
            ->htmlTemplate('registration/confirmation_email.html.twig');

        // Utiliser l'EmailVerifier pour gérer l'envoi avec une URL sécurisée
        $this->emailVerifier->sendEmailConfirmation($routeName, $user, $email);
    }
}
