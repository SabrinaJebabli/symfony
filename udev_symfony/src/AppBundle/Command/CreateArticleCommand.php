<?php
/**
 * Created by PhpStorm.
 * User: Sabrine
 * Date: 26/11/2017
 * Time: 23:15
 */

namespace AppBundle\Command;

use AppBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;



class CreateArticleCommand extends ContainerAwareCommand {

    protected function configure()
    {
        // add configuration
        $this
            // the name of the command (the part after "php bin/console" in command line
            ->setName('udev:create-articles')
            // the short description shown while running "php bin/console"
            ->setDescription('Creates new articles.')
            ->addArgument('article-nb', InputArgument::OPTIONAL,'article number',10);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // do stuff here
        $entityManager=$this->getContainer()->get('doctrine')->getManager();

        // Boucle for pour recuperer automatiquement
        $faker = \Faker\Factory::create();
        $nbarticle=$input->getArgument('article-nb');
        $date=new \DateTime();
        for ($i=1; $i<$nbarticle;$i++) {
            // fake de toutes les variables
            // On cherche ce qui rapproche le plus de ce qu'on veut créer en utilisant:
            //https://github.com/fzaninotto/Faker
            // sinon il suffit de taper  =$faker -> et une liste apparait automatiquement sur php storm des differentes
            // fonctions possibles pour le faker (cf image un peu plus bas)
            // Rappel : nos champs sont: id + subject + body + date
            //id est crée automatiquement donc on ne s'en occupe pas
            // date on a déjà créé au dessus en dehors de la boucle for
            // Ce qui rapproche le plus d'un titre d un article dans les fonctions du faker c'est: sentence(avec int qui donne
            // le nombre de mots de cette phrase)
            // NE SURTOUT PAS PRENDRE title comme je l ai fait personnellement au debut : ca renvoie Miss, Prof, Dr, ...
            $subject=$faker->sentence(6);
            // Ce qui rapproche le plus d'un contenu d'un article dans les fonctions disponibles de $faker c'est la fonction
            // text du lorem ipsum. Elle fonctionne de cette facon text(int) ou int est le nombre de caracteres du texte crée
            $body=$faker->text(200);

            // Creation d un objet article
            $article=new Article();
            // j initialise les attribtus de mon objet article avec les variables du dessus
            // Rappel : nos champs sont: id + subject + body + date
            // mais l'ID est en autoincrementation donc pas besoin de s en charger
            $article->setBody($body);
            $article->setSubject($subject);
            $article->setDate($date);

            // si on veut vérifier le contenu de article
            dump($article);
            //je persiste tous mes articles dans $entitymanager
            $entityManager->persist($article);
        }

        $entityManager->flush();

    }
}

