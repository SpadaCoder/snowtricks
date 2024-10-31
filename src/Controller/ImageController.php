<?php

namespace App\Controller;

use App\Entity\Image;
use App\Service\ImageServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageController extends AbstractController
{
    private ImageServiceInterface $imageService;

    public function __construct(ImageServiceInterface $imageService)
    {
        $this->imageService = $imageService;
    }
    
    #[Route('/image', name: 'app_image')]
    public function index(): Response
    {
        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }

    #[Route('/delete/{id}', name: 'app_image_delete', methods: ['POST'])]
    public function deleteImage(Request $request, Image $image, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $entityManager->remove($image);
            $entityManager->flush();
            $this->imageService->deleteImage($image);
            $this->addFlash('success', 'L\'image a été supprimée.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_trick_edit', ['slug' => $image->getTrick()->getSlug()]);
    }

    #[Route('/edit/{id}', name: 'app_image_edit', methods: ['POST', 'GET'])]
    public function editImage(Request $request, Image $image): Response
    {
        $form = $this->createFormBuilder()
            ->add('imageFile', FileType::class, [
                'label' => 'Sélectionnez une nouvelle image',
                'mapped' => false,
                'required' => true,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newImageFile = $form->get('imageFile')->getData();
            $this->imageService->replaceImage($image, $newImageFile);

            return $this->redirectToRoute('app_trick_edit', ['slug' => $image->getTrick()->getSlug()]);
        }

        return $this->render('image/edit_image.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }
}
