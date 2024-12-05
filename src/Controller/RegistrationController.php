<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserServiceInterface;
use App\Service\EmailServiceInterface;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    private $verifyEmailHelper;
    private $entityManager;

    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, EntityManagerInterface $entityManager)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->entityManager = $entityManager;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserServiceInterface $userService,
        EmailServiceInterface $emailService
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Appeler le service pour gérer l'enregistrement
            $userService->processRegistrationForm($form, $user);

            // Envoi de l'email de validation
            $emailService->sendEmailConfirmation($user, 'app_verify_email', 'no-reply@mailtrap.club');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EmailVerifier $emailVerifier): Response
    {
        try {
            // Valider et extraire les informations de l'URL signée
            $emailVerifier->handleEmailConfirmation($request);
            
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre email a été vérifié !');

        return $this->redirectToRoute('app_login');
    }
}
