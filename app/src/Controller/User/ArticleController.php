<?php

namespace App\Controller\User;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ArticleController extends AbstractController
{
    #[Route('/mes-articles', name: 'user_article_index')]
    public function index(ArticleRepository $articleRepository): Response
    {
        // Récupérer uniquement les articles de l'utilisateur connecté
        $articles = $articleRepository->findBy(['author' => $this->getUser()]);
        
        return $this->render('user/article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/new', name: 'user_article_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $article->setAuthor($this->getUser());
        $article->setCreatedAt(new \DateTimeImmutable());
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article créé avec succès !');
            return $this->redirectToRoute('user_article_index');
        }

        return $this->render('user/article/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/article/{id}/edit', name: 'user_article_edit')]
    public function edit(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        // Vérifier que l'utilisateur est bien l'auteur
        if ($article->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres articles.');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Article modifié avec succès !');
            return $this->redirectToRoute('user_article_index');
        }

        return $this->render('user/article/edit.html.twig', [
            'form' => $form,
            'article' => $article,
        ]);
    }

    #[Route('/article/{id}/delete', name: 'user_article_delete', methods: ['POST'])]
    public function delete(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        // Vérifier que l'utilisateur est bien l'auteur
        if ($article->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres articles.');
        }

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $em->remove($article);
            $em->flush();

            $this->addFlash('success', 'Article supprimé avec succès !');
        }

        return $this->redirectToRoute('user_article_index');
    }

    #[Route('/article/{id}', name: 'user_article_show')]
    public function show(Article $article): Response
    {
        // Vérifier que l'utilisateur est bien l'auteur
        if ($article->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez voir que vos propres articles.');
        }

        return $this->render('user/article/show.html.twig', [
            'article' => $article,
        ]);
    }
}