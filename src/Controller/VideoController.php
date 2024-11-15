<?php

namespace App\Controller;

use App\Entity\Video;
use App\Service\VideoServiceInterface;
use App\Form\VideoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/delete/{id}', name: 'app_video_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Video $video, Request $request): Response
    {
        
        $trick = $this->videoService->removeVideo($video);
        dd($trick);

        return $this->redirectToRoute('app_trick_edit', ['slug' => $trick->getSlug()]);
    }

    #[Route('/edit/{id}', name: 'app_video_edit', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, Video $video): Response
    {
        // Création du formulaire d'édition de l'URL
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $newUrl = $form->get('name')->getData();

            try {
                // Passez la nouvelle URL à editVideo
                $this->videoService->editVideo($video, $newUrl);
                $this->addFlash('success', 'Vidéo mise à jour.');
                return $this->redirectToRoute('app_trick_edit', ['slug' => $video->getTrick()->getSlug()]);
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('video/edit.html.twig', [
            'form' => $form->createView(),
            'video' => $video
        ]);
    }
}
