<?php

namespace App\Security;

use App\Entity\User;
use App\Security\signatureData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {}

    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            $user->getEmail(),
            ['userId' => $user->getId(), 'userEmail' => $user->getEmail()]
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request): void
    {
        // Récupérer les paramètres directement depuis la requête
        $userId = $request->query->get('userId');
        $userEmail = $request->query->get('userEmail');

        // Valider l'URL signée et extraire les paramètres
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, $userId, $userEmail);
        
        // Récupérer l'utilisateur en base
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        
        $user->setVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
