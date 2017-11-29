<?php
/**
 * Created by PhpStorm.
 * User: Sabrine
 * Date: 27/11/2017
 * Time: 10:13
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class UsersController  extends Controller
{

    /**
     * @Route("/users", name="userslist")
     */

    public function usersAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:User');

        $user1 = $repository->find(1);
        $user2 = $repository->findOneBy(['email' => 'roberto65@gmail.com']);
        $users = $repository->findAll();
        $usersDesc = $repository->findBy([], ['id' => 'desc']);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );


        // replace this example code with whatever you need
        return $this->render('users/users.html.twig', [
            'user1' => $user1,
            'user2' => $user2,
            'users' => $pagination,
            'usersDesc' => $usersDesc
        ]);
    }

    /**
     * @Route("/users/add", name="users_add")
     */

    public function addAction(Request $request){

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // replace this example code with whatever you need
            return $this->redirectToRoute('users_show', array('id' => $user->getId()));
        }
        return $this->render('users/add.html.twig',[
            'user'=>$user,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/users/update/{id}", name="users_update")
     */
    public function updateAction(Request $request){

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:User');

        $user = $repository->findOneById(['id'=> $request->get('id')]);

        if (is_null($user)){
            throw $this->createNotFoundException('No user found');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // replace this example code with whatever you need
            return $this->redirectToRoute('users_show', array('id' => $user->getId()));
        }
        return $this->render('users/add.html.twig',[
            'user'=>$user,
            'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/users/show/{id}", name="users_show")
     */

    public function showAction(Request $request) {

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:User');

        $user = $repository->findOneById(['id'=> $request->get('id')]);

        return $this->render('users/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/users/delete/{id}", name="users_delete")
     */

    public function deleteAction(Request $request)    {

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:User');

        $user = $repository->findOneById(['id'=> $request->get('id')]);

        if (is_null($user)){
            throw $this->createNotFoundException('No user found');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('userslist');
    }

    /**
     * @Route("/users/others/{id}", name="users_others")
     */

    public function othersUserWidgetAction(Request $request, $id) {

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:User');

        $queryBuilder = $repository ->createQueryBuilder('u')
                ->where("u.id != :id")
                ->setParameters(['id' => $id])
                ->setMaxResults(10);
        $query = $queryBuilder->getQuery();
        $users = $query->getResult();

        return $this->render('users/others.html.twig', [
            'user' => $users
        ]);
    }

    /**
     * @Route("/users/today", name="users_today")
     */

    public function todayUserWidgetAction(Request $request) {

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('AppBundle:User');
        $dateCreate = new \DateTime();

        $queryBuilder = $repository ->createQueryBuilder('u')
            ->where("u.dateCreate = :dateCreate")
            ->setParameters(['dateCreate'=> $dateCreate->format('Y-m-d')])
            ->setMaxResults(10);
        $query = $queryBuilder->getQuery();
        $users = $query->getResult();

        return $this->render('users/today.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/users/fluxrss", name="users_fluxrss")
     */
    public function fluxrssWidgetAction(Request $request) {
        // create a simple FeedIo instance
        $feedIo = \FeedIo\Factory::create()->getFeedIo();
        $url = "http://www.lemonde.fr/pixels/rss_full.xml";

        // read a feed since a certain date
        $result = $feedIo->readSince($url, new \DateTime('-7 days'));

        // get title
        $feedTitle = $result->getFeed()->getTitle();

        // iterate through items
        foreach( $result->getFeed() as $item ) {
            echo $item->getTitle();
        }

        return $this->render('::fluxrss.html.twig', [
            'result' => $result->getFeed()
        ]);
    }
}