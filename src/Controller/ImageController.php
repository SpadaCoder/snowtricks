<?php

namespace App\Controller;

use App\Entity\Image;
use App\Service\ImageServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


class ImageController extends AbstractController
{
    private ImageServiceInterface $imageService;

    public function __construct(
        ImageServiceInterface $imageService,
        private readonly CsrfTokenManagerInterface $csrfTokenManager
         )
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

    #[Route('/image/confirm-delete/{id}', name: 'app_image_confirm_delete', methods: ['GET'])]
public function confirmDelete(Image $image, CsrfTokenManagerInterface $csrfTokenManager): Response
{
    $csrfToken = $csrfTokenManager->getToken('delete' . $image->getId())->getValue();

    return $this->render('image/confirm_delete.html.twig', [
        'image' => $image,
        'csrf_token' => $csrfToken,
    ]);
}

    #[Route('/image/delete/{id}', name: 'app_image_delete', methods: ['POST'])]
    public function deleteImage(Request $request, Image $image, CsrfTokenManagerInterface $csrfTokenManager): RedirectResponse
    {
// Récupérer le slug de l'image ou de son trick
$slug = $image->getTrick()->getSlug(); 

// Validation du token CSRF
if ($csrfTokenManager->isTokenValid(new CsrfToken('delete' . $image->getId(), $request->request->get('_token')))) {
    $this->imageService->deleteImage($image);
    $this->addFlash('success', 'L\'image a été supprimée.');
} else {
    $this->addFlash('error', 'Token CSRF invalide.');
}

        return $this->redirectToRoute('app_trick_edit', ['slug' => $slug]);
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
