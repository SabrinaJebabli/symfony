<?php
/**
 * Created by PhpStorm.
 * User: Sabrine
 * Date: 28/11/2017
 * Time: 14:02
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function articlesAddAction(Request $request){

        $article = new Article();
        $form = $this->createForm('AppBundle\Form\ArticleType', $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            // replace this example code with whatever you need
            return $this->redirectToRoute('articles_show', array('id' => $article->getId()));
        }
        return $this->render(':articles:articleadd.html.twig',[
            'article'=>$article,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/articles/update/{id}", name="articles_update")
     */

    public function articlesUpdateAction(Request $request){

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:Article');

        $article = $repository->findOneById(['id'=> $request->get('id')]);

        if (is_null($article)){
            throw $this->createNotFoundException('No article found');
        }

        $form = $this->createForm('AppBundle\Form\ArticleType', $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            // replace this example code with whatever you need
            return $this->redirectToRoute('articles_show', array('id' => $article->getId()));
        }
        return $this->render(':articles:articleadd.html.twig',[
            'article'=>$article,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/articles/delete/{id}", name="articles_delete")
     */

    public function articlesDeleteAction(Request $request)    {

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:Article');

        $article = $repository->findOneById(['id'=> $request->get('id')]);

        if (is_null($article)){
            throw $this->createNotFoundException('No article found');
        }

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('articleslist');
    }

    /**
     * @Route("/artciles/others/{id}", name="articles_others")
     */

    public function othersArticlesWidgetAction(Request $request, $id) {

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:Article');

        $queryBuilder = $repository ->createQueryBuilder('u')
            ->where("u.id != :id")
            ->setParameters(['id' => $id])
            ->setMaxResults(10);
        $query = $queryBuilder->getQuery();
        $articles = $query->getResult();

        return $this->render(':articles:articlesothers.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/articles/today", name="articles_today")
     */

    public function todayArticlesWidgetAction(Request $request) {

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:User');
        $dateCreate = new \DateTime();

        $queryBuilder = $repository ->createQueryBuilder('u')
            ->where("u.dateCreate = :dateCreate")
            ->setParameters(['dateCreate'=> $dateCreate->format('Y-m-d')])
            ->setMaxResults(10);
        $query = $queryBuilder->getQuery();
        $articles = $query->getResult();

        return $this->render(':articles:articlestoday.html.twig', [
            'articles' => $articles
        ]);
    }
}