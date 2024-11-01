<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Image;
use App\Form\TrickType;
use App\Service\TrickServiceInterface;
use App\Service\ImageServiceInterface;
use App\Repository\TrickRepository;
use App\Service\VideoServiceInterface;
use Doctrine\ORM\Cache\Persister\Collection\ReadOnlyCachedCollectionPersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trick')]
class TrickController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly TrickServiceInterface $trickService,
        private readonly ImageServiceInterface $imageService,
        private readonly EntityManagerInterface $entityManagerInterface,
        private readonly VideoServiceInterface $videoService

    ) {
    }


    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ImageServiceInterface $imageService, EntityManagerInterface $entityManager, TrickRepository $trickRepository, string $slug = null): Response
    {
        // Soit créer un nouveau Trick, soit récupérer celui qui correspond au slug
        $trick = new Trick();
       
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'utilisateur authentifié au trick.
            $userId = 1; //TO DO
            //$user = $this->getUser(); //TO DO
            $user = $entityManager->getRepository(User::class)->find($userId);



            // Appeler le service Trick pour gérer la création du trick et l'upload des images
            $this->trickService->create($trick, $form, $user);

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'isEdit' => false, // Mode création
        ]);
    }

    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET'])]
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'featuredImage' => $this->trickService->getFeaturedImageOrDefault($trick)
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Trick $trick, EntityManagerInterface $entityManager, VideoServiceInterface $videoService): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        // Initialisation de la variable $videoEmbedUrls
        $videoEmbedUrls = [];

        if ($form->isSubmitted() && $form->isValid()) {

            // Associer l'utilisateur authentifié au trick.
            $userId = 1; //TO DO
            //$user = $this->getUser(); //TO DO
            $user = $entityManager->getRepository(User::class)->find($userId);

            // Appeler le service Trick pour gérer la mise à jour du trick et l'upload des nouvelles images
            $this->trickService->update($trick, $form, $user);

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'isEdit' => true, // Mode édition
            'featuredImage' => $this->trickService->getFeaturedImageOrDefault($trick)
        ]);
    }

    #[Route('/{slug}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->getPayload()->getString('_token'))) {
            // Appeler le service Trick pour supprimer le trick et ses images associées
            $this->trickService->delete($trick);
        }

        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/image/delete/{id}', name: 'app_trick_delete_image', methods: ['POST'])]
    public function deleteImage(Request $request, Image $image, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            // Supprimer l'image physiquement et de la base de données
            $entityManager->remove($image);
            $entityManager->flush();

            // Supprimer le fichier physiquement (si nécessaire)
            $this->imageService->deleteImage($image);

            $this->addFlash('success', 'L\'image a été supprimée.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        // Redirection vers la même page (ou une autre page selon le contexte)
        return $this->redirectToRoute('app_trick_edit', ['slug' => $image->getTrick()->getSlug()]);
    }

    #[Route('/image/edit/{id}', name: 'app_trick_edit_image', methods: ['POST'])]
    public function editImage(Request $request, Image $image, ImageServiceInterface $imageService): Response
    {
        // Création d'un formulaire pour l'upload d'une nouvelle image
        $form = $this->createFormBuilder()
            ->add('imageFile', FileType::class, [
                'label' => 'Sélectionnez une nouvelle image',
                'mapped' => false,
                'required' => true,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Supprimer l'ancienne image et uploader la nouvelle
            $newImageFile = $form->get('imageFile')->getData();
            $this->imageService->replaceImage($image, $newImageFile);

            // Redirection vers la page d'édition du trick
            return $this->redirectToRoute('app_trick_edit', ['slug' => $image->getTrick()->getSlug()]);
        }

        return $this->render('trick/edit_image.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }

}
