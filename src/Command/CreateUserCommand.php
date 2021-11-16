<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Entity\ManagerInterface;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand( name: 'app:create-user', description: 'Crear Usuario con la consola de comandos',)]
class CreateUserCommand extends Command {

    protected static $defaultName = 'app:create-user';

    //Propiedades
    private $em;
    private $userRepository;
    private $userPasswordhasher;

    public function __construct( EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository ){

        parent::__construct();

        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;

    }


    protected function configure(): void
    {
        $this->setDescription('Comando para crear usuarios en la DB')
            ->setHelp('Parámetros necesario: email, displayName y password')
            ->addArgument('email', InputArgument::REQUIRED, 'EMAIL')
            ->addArgument('displayName', InputArgument::REQUIRED, 'Nick')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        // return Command::SUCCESS;
        $output->writeln('<fg=white;bg=black>Crear Usuario</>');

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $displayName = $input->getArgument('displayName');

        if( $this->userRepository->findOneBy(['email'=> $email ])){
            $output->writeln("<error>El usuario con el mail $email ya está registrado en la DB</error>");
            return Command::FAILURE;
        }

        $user = (new User())->setEmail($email)->setdisplayName($displayName);

        $hashedPassword = $this->passwordHasher->hashPassword( $user, $password);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln("<fg=white;bg=green>Usuario con el mail $email guardado correctamente en la DB</>");
            return Command::SUCCESS;

    }
}
