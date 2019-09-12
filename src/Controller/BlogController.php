<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\FormArticleType;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        // recuperer les articles de la base
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home() {
        return $this->render('blog/home.html.twig', [
            'title' => "Bienvenue sur ce blog réalisé avec PHP Symfony!"
            ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager) {
        
        // si il n'y a pas d'article saisi en paramètre de la requete, instanciation nouvel article
        if(!$article){
            $article = new Article();
        }

        // crée un objet form lié à l'objet article
        $form = $this->createForm(FormArticleType::class, $article);

        // pour analyser les données retournées par le formulaire
        $form->handleRequest($request);

        // persistance des données du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        // on passe la vue du form à twig
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article) {
    //public function show($id) {
        // recuperation d'un article de la base
        //$repo = $this->getDoctrine()->getRepository(Article::class);
        //$article = $repo->find($id);

        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }
}
