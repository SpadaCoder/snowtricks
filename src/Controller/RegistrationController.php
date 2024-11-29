<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserServiceInterface;
use App\Service\EmailServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
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
            $emailService->sendEmailConfirmation($user, 'app_verify_email', 'snowtricks@gmx.com');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * @Route("/verify/email/{token}", name="app_verify_email")
     */
    public function verifyEmail(string $token): Response
    {
        // Logique pour vérifier le token et activer l'utilisateur
        // Exemple de recherche d'un utilisateur par le token et activation
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['verificationToken' => $token]);

        if ($user) {
            $user->setIsVerified(true);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_home');  // Rediriger vers la page d'accueil après confirmation
        }

        // Gestion des erreurs si le token est invalide
        return $this->render('registration/error.html.twig');
    }
}
