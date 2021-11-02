<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ActorRepository;
use DateTime;

#[AsCommand(
    name: 'app:CreateActor',
    description: 'Add a short description for your command',
)]
class CreateActorCommand extends Command {

    //PROPIEDADES
    private $entityManager;
    private $actorRepository;

    public function __construct( EntityManagerInterface $em, ActorRepository $ar ){

        parent::__construct();
        $this->entityManager = $em;
        $this->actorRepository = $ar;
    }


    protected function configure(): void
    {
        $this->setDescription('Comando para añadir Actores por consola')
            ->setHelp('Los parámetros son NOMBRE (requerido), FEACHA NACIMIENTO, BIOGRAFIA, NACIONALIDAD ')
            ->addArgument('nombre', InputArgument::REQUIRED, 'Nombre:')
            ->addArgument('fecha_nacimiento', InputArgument::OPTIONAL, 'FECHA NACIMIENTO:')
            ->addArgument('biografia', InputArgument::OPTIONAL, 'BIOGRAFIA:')
            ->addArgument('nacionalidad', InputArgument::OPTIONAL, 'NACIONALIDAD:')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
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
        $output->writeln('<fg=white;bg=black>CREAR ACTOR</>');

        $nombre= $input->getArgument('nombre');
        $fecha_nacimiento = $input->getArgument('fecha_nacimiento');
        // dd($fechanacimiento);
        $nacimiento = new \DateTime( $fecha_nacimiento);
        $nacionalidad = $input->getArgument('nacionalidad');
        $biografia = $input->getArgument('biografia');

        $actor = new Actor();
        $actor->setNombre($nombre)
                ->setNacionalidad($nacionalidad)
                ->setFechaNacimiento($nacimiento)
                ->setBiografia($biografia);

        $this->entityManager->persist($actor);
        $this->entityManager->flush();

        $output->writeln("<fg=white;bg=black>Actor $nombre CREADO</>");

        return Command::SUCCESS;
    }
}
