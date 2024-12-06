<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\ForgotType;
use App\Repository\UserRepository;
use App\Service\EmailService;

class SecurityController extends AbstractController
{
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, UserRepository $userRepository) : Response
    {
        // Création du formulaire de réinitialisation
        $form = $this->createForm(ForgotType::class);

        // Gestion de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Recherche de l'utilisateur dans la base
            $user = $userRepository->findOneByEmail($form->getData()['email']);

            // Verification si on a un utilisateur
            if (!$user) {
                // Ajoutez un message flash ou une erreur si l'email n'existe pas
                $this->addFlash('danger', 'Compte inexistant.');
                return $this->redirectToRoute('app_login');
            } else {
                // Envoi du lien de réinitialisation du mot de passe
                $this->emailService->sendPasswordResetEmail(
                    $user,
                    'app_reset_password', 
                    'no-reply@snowtricks.com' 
                );

                // Message de confirmation pour l'utilisateur
                $this->addFlash('success', 'Un lien de réinitialisation a été envoyé.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/forgot.html.twig', [
            'requestPassForm' => $form->createView(),
        ]);
    }
}
