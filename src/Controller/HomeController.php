<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\TrickServiceInterface;

class HomeController extends AbstractController
{
    public function __construct(TrickServiceInterface $trickService)
    {
        $this->trickServiceInterface = $trickService;
    }

    #[Route(path: '/', name: 'app_home')]
    public function index(TrickRepository $trickRepository, TrickServiceInterface $trickService): Response
    {
        // Récupère les tricks depuis la base de données
        $tricks = $trickRepository->findAll();
        $tricksWithImages = [];

        foreach ($tricks as $trick) {
            $tricksWithImages[] = [
                'trick' => $trick,
                'featuredImage' => $this->trickServiceInterface->getFeaturedImageOrDefault($trick),
            ];
        }

        // Rendu de la vue Twig en passant les tricks
        return $this->render('home.html.twig', [
            'tricks' => $tricksWithImages,
        ]);
    }

}
