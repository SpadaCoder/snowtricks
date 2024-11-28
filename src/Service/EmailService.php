<?php

namespace App\Service;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class EmailService implements EmailServiceInterface
{
    public function __construct(private EmailVerifier $emailVerifier) {}

    public function sendEmailConfirmation(User $user, string $routeName, string $fromEmail): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($fromEmail, 'Snowtricks Mail Bot'))
            ->to($user->getEmail())
            ->subject('Merci de confirmer votre Email')
            ->htmlTemplate('registration/confirmation_email.html.twig');

        $this->emailVerifier->sendEmailConfirmation($routeName, $user, $email);
    }
}
