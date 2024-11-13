<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\TrickServiceInterface;

class HomeController extends AbstractController
{
    public function __construct(TrickServiceInterface $trickService)
    {
        $this->trickService = $trickService;
    }

    #[Route(path: '/', name: 'app_home')]
    public function index(): Response
    {
        // Récupère les tricks depuis la base de données
        $tricks = $this->trickService->findAll();
        $tricksWithImages = [];

        foreach ($tricks as $trick) {
            $tricksWithImages[] = [
                'trick' => $trick,
                'featuredImage' => $this->trickService->getFeaturedImageOrDefault($trick),
            ];
        }

        // Rendu de la vue Twig en passant les tricks
        return $this->render('home.html.twig', [
            'tricks' => $tricksWithImages,
        ]);
    }

}
