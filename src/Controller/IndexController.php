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
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Entity\PriceSearch;
use App\Form\PriceSearchType;


class IndexController extends AbstractController
{
    #[Route("/", name: "homepage")]
    public function home(ArticleRepository $articleRepository, Request $request): Response
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);
    
        $articles = [];
    
        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $search->getNom();
    
            if ($nom !== null && $nom !== '') {
                $articles = $articleRepository->findBy(['nom' => $nom]);
            } else {
                $articles = $articleRepository->findAll();
            }
        }
    
        return $this->render('articles/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
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
            return $this->redirectToRoute('homepage');
        }
    
        return $this->render('articles/newCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/art_cat/', name: 'article_par_cat', methods: ['GET', 'POST'])]
public function articlesParCategorie(ArticleRepository $articleRepository,Request $request): Response
{
    $categorySearch = new CategorySearch();
    $form = $this->createForm(CategorySearchType::class, $categorySearch);
    $form->handleRequest($request);

    $articles = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $category = $categorySearch->getCategory();

        if ($category !== null) {
            $articles = $category->getArticles();
        } else {
            $articles = $articleRepository->findAll();

        }
       
    }

    return $this->render('articles/articlesParCategorie.html.twig', [
        'form' => $form->createView(),
        'articles' => $articles,
    ]);
    
}
#[Route('/art_prix', name: 'article_par_prix', methods: ['GET'])]
     public function articlesParPrix(Request $request, ArticleRepository $articleRepository): Response
{
    $priceSearch = new PriceSearch();
    $form = $this->createForm(PriceSearchType::class, $priceSearch);
    $form->handleRequest($request);

    $articles = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $minPrice = $priceSearch->getMinPrice();
        $maxPrice = $priceSearch->getMaxPrice();

        $articles = $articleRepository->findByPriceRange($minPrice, $maxPrice);
        
    }


    return $this->render('articles/articlesParPrix.html.twig', [
        'form' => $form->createView(),
        'articles' => $articles
    ]);
}

}





