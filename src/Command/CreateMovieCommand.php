<?php

namespace App\Command;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:create-movie',
    description: 'Add a short description for your command',
)]
class CreateMovieCommand extends Command {

    //PROPIEDADES
    private $entityManager;
    // private $movieRepositoriy;

    public function __construct( EntityManagerInterface $em, MovieRepository $mr ){

        parent::__construct();
        $this->entityManager = $em;
        // $this->movieRepositoriy = $mr;
    }


    protected function configure(): void
    {
        $this->setDescription('Comando para añadir peliculas por consola')
            ->setHelp('Los parámetros son TITULO (requerido), DURACIÓN, DIRECTOR, GÉNERO, ESTRENO, VALORACIÓN, SINOPSIS ')
            ->addArgument('titulo', InputArgument::REQUIRED, 'TITULO:')
            ->addArgument('duracion', InputArgument::OPTIONAL, 'DURACION:')
            ->addArgument('director', InputArgument::OPTIONAL, 'DIRECTOR:')
            ->addArgument('genero', InputArgument::OPTIONAL, 'GENERO:')
            ->addArgument('estreno', InputArgument::OPTIONAL, 'ESTRENO:')
            ->addArgument('valoracion', InputArgument::OPTIONAL, 'VALORACION:')
            ->addArgument('sinopsis', InputArgument::OPTIONAL, 'SINOPSIS:')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('titulo');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        $output->writeln('<fg=white;bg=black>CREAR PELICULA</>');

        $titulo= $input->getArgument('titulo');
        $duracion = $input->getArgument('duracion');
        $director = $input->getArgument('director');
        $genero = $input->getArgument('genero');
        $estreno = $input->getArgument('estreno');
        $valoracion = $input->getArgument('valoracion');
        $sinopsis = $input->getArgument('sinopsis');

        $peli = new Movie();
        $peli->setTitulo($titulo)
                ->setDirector($director)
                ->setDuracion($duracion)
                ->setGenero($genero)
                ->setEstreno($estreno)
                ->setValoracion($valoracion)
                ->setSinopsis($sinopsis);

        $this->entityManager->persist($peli);
        $this->entityManager->flush();

        $output->writeln("<fg=white;bg=black>PELÍCULA $titulo CREADA</>");


        return Command::SUCCESS;
    }
}
