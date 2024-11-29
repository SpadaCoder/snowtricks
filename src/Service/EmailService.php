<?php

namespace App\Service;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class EmailService implements EmailServiceInterface
{
    public function __construct(private EmailVerifier $emailVerifier) {}

    public function sendEmailConfirmation(User $user, string $routeName, string $fromEmail): void
    {
         // Générer l'URL de la route de vérification d'email avec le token
         $url = $this->urlGenerator->generate($routeName, ['token' => $user->getVerificationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new TemplatedEmail())
            ->from(new Address($fromEmail, 'Snowtricks Mail Bot'))
            ->to($user->getEmail())
            ->subject('Merci de confirmer votre Email')
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context([
                'verificationUrl' => $url  // Passer l'URL de vérification à la vue
            ]);

        $this->emailVerifier->sendEmailConfirmation($routeName, $user, $email);
    }
}
