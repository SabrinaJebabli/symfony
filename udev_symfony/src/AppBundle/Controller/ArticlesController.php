<?php
/**
 * Created by PhpStorm.
 * User: Sabrine
 * Date: 28/11/2017
 * Time: 14:02
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class ArticlesController  extends Controller {

    /**
     *
     * @Route("/articles", name="articleslist")
     */

    public function articleAction(Request $request)
    {
        // afficher elements de la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $repositery = $entityManager->getRepository('AppBundle:Article');


        $articles = $repositery->findBy([], ['id' => 'desc']);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            4/*limit per page*/
        );


        return $this->render('articles/articles.html.twig', [
            'articles' => $articles,
            'articles' => $pagination,
        ]);
    }

    /**
     * @Route("/articles/show/{id}", name="articles_show")
     */

    public function articlesShowAction(Request $request) {

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:Article');

        $article = $repository->findOneById(['id'=> $request->get('id')]);

        return $this->render('articles/articleshow.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/articles/add", name="articles_add")
     */

    public function addAction(Request $request){

        $article = new Article();

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $article);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('id',      NumberType::class)
            ->add('subject',     TextType::class)
            ->add('body',   TextareaType::class)
            ->add('save',      SubmitType::class)
        ;
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            // replace this example code with whatever you need
            return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }
        return $this->render('articles/articleadd.html.twig',[
            'article'=>$article,
            'form'=>$form->createView()
        ]);
    }
}