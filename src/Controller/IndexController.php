<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\ArticleType;


class IndexController extends AbstractController
{
    #[Route("/", name: "homepage")]
    public function home(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route("/article/save", name: "article_save")]
    public function save(EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $article->setNom('Article 3');
        $article->setPrix(3000);

        $entityManager->persist($article);
        $entityManager->flush();

        return new Response('Article enregistré avec ID ' . $article->getId());
    }
    #[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
   public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
    $article = new Article();

    $form = $this->createForm(ArticleType::class, $article);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $article = $form->getData();

        $entityManager->persist($article);
        $entityManager->flush();

        return $this->redirectToRoute('homepage');
    }

    return $this->render('articles/new.html.twig', [
        'form' => $form->createView(),
    ]);
   }
   #[Route('/article/{id}', name: 'article_show')]
     public function show(int $id, ArticleRepository $articleRepository): Response
    {
    $article = $articleRepository->find($id);

    if (!$article) {
        throw $this->createNotFoundException('Article non trouvé');
    }

    return $this->render('articles/show.html.twig', [
        'article' => $article,
    ]);
}
#[Route("/article/edit/{id}", name: "edit_article", methods: ['GET', 'POST'])]
public function edit(Request $request, $id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
{
    $article = $articleRepository->find($id);

    if (!$article) {
        throw $this->createNotFoundException('Article non trouvé');
    }

    $form = $this->createFormBuilder($article)
        ->add('nom', TextType::class)
        ->add('prix', TextType::class)
        ->add('save', SubmitType::class, [
            'label' => 'Modifier'
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('homepage'); 
    }

    return $this->render('articles/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route("/article/delete/{id}", name: "delete_article", methods: ['GET', 'POST'])]
    public function delete($id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('homepage');
    }
    #[Route('/category/newCat', name: 'new_category', methods: ['GET', 'POST'])]
    public function newCategory(Request $request,EntityManagerInterface $entityManager) : Response{
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
        }
    
        return $this->render('articles/newCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }

}




