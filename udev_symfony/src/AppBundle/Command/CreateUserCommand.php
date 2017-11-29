<?php
namespace AppBundle\Command;

use AppBundle\Entity\User;
use Doctrine\ORM\Query\AST\Functions\CurrentDateFunction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class CreateUserCommand extends ContainerAwareCommand {
    protected function configure(){
        $this
        // the name of the commad(the part after " bin/console "
        ->setName('udev:create-users')
        // the sort descrition shown while running "php bin/console"
        ->setDescription('Creates new users.')
        ->addArgument('user-nb',InputArgument::OPTIONAL, 'user number', 10);
        ;

    }
    protected function execute(InputInterface $input, OutputInterface $output){
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $faker = \Faker\Factory::create();
        $nbuser = $input->getArgument( 'user-nb');

        for ($i=1; $i<= $nbuser; $i++ ){

            $username = $faker->lastName;
            $usermail = $faker->email;
            $userlogin = $faker->userName;
            $userpassword = $faker->password;
            $userdate = new \DateTime();


            $user = new User();
            $user->setUsername($username);
            $user->setEmail($usermail);
            $user->setLogin($userlogin);
            $user->setPassword($userpassword);
            $user->setDateCreate($userdate);
            //J'ffiche tous mes users dans
            $output->writeln($user->getUsername());
            $entityManager->persist($user);

        }

        $entityManager->flush();



    }

    }

