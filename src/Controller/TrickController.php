<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Image;
use App\Form\TrickType;
use App\Service\TrickServiceInterface;
use App\Service\ImageServiceInterface;
use App\Repository\TrickRepository;
use App\Service\VideoServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Form\CommentType;

#[Route('/trick')]
class TrickController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly TrickServiceInterface $trickService,
        private readonly ImageServiceInterface $imageService,
        private readonly EntityManagerInterface $entityManagerInterface,
        private readonly VideoServiceInterface $videoService,
        private readonly CommentRepository $commentRepository
    ) {
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, ImageServiceInterface $imageService, EntityManagerInterface $entityManager, TrickRepository $trickRepository, string $slug = null): Response
    {
        // Soit créer un nouveau Trick, soit récupérer celui qui correspond au slug
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Appeler le service Trick pour gérer la création du trick et l'upload des images
            $this->trickService->create($trick, $form, $this->getUser());

            // Message flash
            $this->addFlash('success', 'Le trick a été enregistré avec succès !');

            return $this->redirectToRoute('app_home', ['_fragment' => 'tricks-list'], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'isEdit' => false, // Mode création
        ]);
    }

    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Trick $trick, Request $request): Response
    {
        $page = $request->query->getInt('page', 1); // Récupère le numéro de la page, par défaut 1
        $limit = 10; // Nombre de commentaires par page
        $offset = ($page - 1) * $limit; // Calcul de l'offset pour la requête

        // Récupérer tous les commentaires pour le trick donné
        $comments = $this->commentRepository->findBy(['trick' => $trick], ['created' => 'DESC'], $limit, $offset);

        // Compter le total de commentaires pour calculer le nombre de pages
        $totalComments = count($this->commentRepository->findBy(['trick' => $trick]));
        $totalPages = ceil($totalComments / $limit); // Calculer le nombre total de pages
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setTrick($trick);
                $comment->setUser($this->getUser());
                $comment->setCreated(new \DateTimeImmutable());
                $comment->setModified(new \DateTimeImmutable());

                $this->entityManagerInterface->persist($comment);
                $this->entityManagerInterface->flush();

                return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
            }
        }


        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'featuredImage' => $this->trickService->getFeaturedImageOrDefault($trick),
            'comments' => $comments,
            'form' => $form->createView(),
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, Trick $trick, EntityManagerInterface $entityManager, VideoServiceInterface $videoService): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        // Initialisation de la variable $videoEmbedUrls
        $videoEmbedUrls = [];

        if ($form->isSubmitted() && $form->isValid()) {

            // Associer l'utilisateur authentifié au trick.
            $user = $this->getUser();

            // Appeler le service Trick pour gérer la mise à jour du trick et l'upload des nouvelles images
            $this->trickService->update($trick, $form, $user);

            // Message flash
            $this->addFlash('success', 'Le trick a été enregistré avec succès !');

            return $this->redirectToRoute('app_home', ['_fragment' => 'tricks-list'], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'featuredImage' => $this->trickService->getFeaturedImageOrDefault($trick)
        ]);
    }

    #[Route('/{slug}', name: 'app_trick_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Request $request, Trick $trick): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            // Appeler le service Trick pour supprimer le trick et ses images associées
            $this->trickService->delete($trick);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

}
