<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateAdminCommand extends ContainerAwareCommand
{
    const DATE_FORMAT = 'Y-m-d';
    protected function configure()
    {
        $this
            ->setName('create:admin')
            ->setDescription('Creates a new admin user')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try{
            $em = $this->getContainer()->get('doctrine')->getManager();

            $name = $this->getAnswer($input, $output, "Name: ", "Name can't be empty");

            $email = $this->getAnswer($input, $output, "e-mail address (must be a valid address): ", "email can't be empty");
            $existing_user = $em->getRepository('AppBundle:User')->findOneByEmail($email);
            while(!filter_var($email, FILTER_VALIDATE_EMAIL) || $existing_user){
                if($existing_user){
                    $output->writeln("E-Mail already taken");
                }else{
                    $output->writeln("E-Mail not valid");
                }
                $email = $this->getAnswer($input, $output, "e-mail address (must be a valid address): ", "email can't be empty");
                $existing_user = $em->getRepository('AppBundle:User')->findOneByEmail($email);
            }

            $password = $this->getAnswer($input, $output, "Password: ", "Password can't be empty");
            $confirm = $this->getAnswer($input, $output, "Confirm Password: ", "Confirmation can't be empty");
            while($password != $confirm || strlen($password) > 4096){
                if(strlen($password) > 4096){
                    $output->writeln("Password is too long (max. 4096 characters)");
                }else{
                    $output->writeln("Passwords don't match");
                }
                $password = $this->getAnswer($input, $output, "Password: ", "Password can't be empty");
                $confirm = $this->getAnswer($input, $output, "Confirm Password: ", "Confirmation can't be empty");
            }

            $user = new User();
            $user->setIsActive(true);
            $user->setName($name);
            $user->setEmail($email);
            $user->setUsername($email);
            $user->setRoles(array("ROLE_ADMIN"));
            //create hashed password
            $hashed_password = $this->getContainer()->get('security.password_encoder')
                ->encodePassword($user, $password);
            //set password
            $user->setPassword($hashed_password);
            $em->persist($user);
            $em->flush();

            $output->writeln('User created');

        }catch(Exception $exception){
            $output->writeln('There was an error creating the user');
        }finally{
            //
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $questionText
     * @param string $errorText
     * @return string $answer
     */
    protected function getAnswer($input, $output, $questionText, $errorText){
        $answer = "";
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new Question($questionText);
        while($answer == ""){
            $answer = $helper->ask($input, $output, $question);
            if($answer == ""){
                $output->writeln($errorText);
            }
        }
        return $answer;
    }
}
