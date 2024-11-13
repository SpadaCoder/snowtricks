<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\ForgotType;
use App\Repository\UserRepository;

class SecurityController extends AbstractController
{
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

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, UserRepository $userRepository)
    {
        // Création du formulaire de réinitialisation
        $form = $this->createForm(ForgotType::class);

         // Gestion de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];

             // Vérification de l'existence de l'email
             $user = $userRepository->findOneByEmail($email);
             if (!$user) {
                 // Ajoutez un message flash ou une erreur si l'email n'existe pas
                 $this->addFlash('danger', 'Compte inexistant.');
             } else {
                 // Logique pour envoyer le lien de réinitialisation


                 
                 // Message de confirmation pour l'utilisateur
                 $this->addFlash('success', 'Un lien de réinitialisation a été envoyé.');
                 return $this->redirectToRoute('app_login');
             }
        }

        return $this->render('security/forgot.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
