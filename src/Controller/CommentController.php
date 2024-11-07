<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private $entityManager;
    private $commentRepository;

    public function __construct(EntityManagerInterface $entityManager, CommentRepository $commentRepository)
    {
        $this->entityManager = $entityManager;
        $this->commentRepository = $commentRepository;
    }

    #[Route('/trick/{slug}/comments', name: 'app_trick_comments')]
    public function index(Trick $trick, Request $request): Response
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

        // if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());
            $comment->setCreated(new \DateTimeImmutable());
            $comment->setModified(new \DateTimeImmutable());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_trick_comments', ['slug' => $trick->getSlug()]);
        }
        // }

        return $this->render('comment/index.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'form' => $form->createView(),
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }
}
