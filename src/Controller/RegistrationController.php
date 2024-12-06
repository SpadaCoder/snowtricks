<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordType;
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
    private $userService;

    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, EntityManagerInterface $entityManager, UserServiceInterface $userService)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
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
            $emailService->sendEmailConfirmation($user, 'app_verify_email', 'no-reply@snowtricks.com');

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
            $user=$emailVerifier->handleEmailConfirmation($request);
            $this->userService->verifyUser($user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre email a été vérifié !');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/reset-password', name: 'app_reset_password', methods : ['GET', 'POST'])]
    public function resetPassword(Request $request, EmailVerifier $emailVerifier): Response
    {

        try {
            // Valider et extraire les informations de l'URL signée
            $user=$emailVerifier->handleEmailConfirmation($request);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('app_forgot_password');
        }
        
        $this->addFlash('success', 'Vous pouvez réinitialiser votre mot de passe !');

        // Créer et traiter le formulaire de réinitialisation du mot de passe
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->setUserPassword($user, $form->getData()['password']);
            $this->userService->save($user);
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/password_reset.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }
}
