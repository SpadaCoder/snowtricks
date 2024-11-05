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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
    public function delete(Video $video): Response
    {
        //autorisation
        $this->videoService->removeVideo($video);
        $this->addFlash('success', 'Vidéo supprimée.');
        
        return $this->redirectToRoute('app_trick_edit', ['slug' => $video->getTrick()->getSlug()]);
    }
    
    #[Route('/edit/{id}', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video): Response
    {
        //autorisation

         // Création du formulaire d'édition de l'URL
    $form = $this->createFormBuilder($video)
    ->add('name', TextType::class, [
        'label' => 'URL de la vidéo',
        'attr' => ['class' => 'form-control']
    ])
    ->add('save', SubmitType::class, [
        'label' => 'Enregistrer',
        'attr' => ['class' => 'btn btn-primary']
    ])
    ->getForm();

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
    
        return $this->render('video/edit.html.twig', [
            'form' => $form->createView(),
            'video' => $video
        ]);
    }



    }
}
