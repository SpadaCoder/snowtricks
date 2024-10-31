<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Trick;
use App\Service\VideoServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;

#[Route('/video')]
class VideoController extends AbstractController
{
    private VideoServiceInterface $videoService;
    private $trickRepository;

    public function __construct(VideoServiceInterface $videoService, TrickRepository $trickRepository)
    {
        $this->videoService = $videoService;
        $this->trickRepository = $trickRepository;
    }

    #[Route('/add/{trickId}', name: 'app_video_add', methods: ['POST'])]
    public function add(Request $request, int $trickId): Response
    {
        // Récupérer l'entité Trick à partir du repository
        $trick = $this->trickRepository->find($trickId);

        if (!$trick) {
            throw $this->createNotFoundException('Le trick spécifié est introuvable.');
        }

        $url = $request->request->get('video_url');
        $provider = $this->videoService->getProviderFromUrl($url);

        if ($provider) {
            $this->videoService->addVideo($url, $provider, $trick);
            $this->addFlash('success', 'Vidéo ajoutée avec succès.');
        } else {
            $this->addFlash('error', 'Fournisseur vidéo non reconnu.');
        }

        return $this->redirectToRoute('app_trick_edit', ['slug' => $trick->getSlug()]);
    }

    #[Route('/delete/{id}', name: 'app_video_delete', methods: ['POST'])]
    public function delete(Video $video): Response
    {
        $this->videoService->removeVideo($video);
        $this->addFlash('success', 'Vidéo supprimée.');

        return $this->redirectToRoute('app_trick_edit', ['slug' => $video->getTrick()->getSlug()]);
    }
}
