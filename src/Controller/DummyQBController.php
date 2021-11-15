<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DummyQBController extends AbstractController
{
    #[Route('/dummy_qb', name: 'dummy_qb')]
    public function index(): Response
    {
        return $this->render('dummy_qb/index.html.twig', [
            
        ]);
    }
    

    #[Route('/dummy/qb1', name: 'dummy_qb1')]
    public function dummy_qb1( EntityManagerInterface $entityManager ): Response
    {
        $pelis = $entityManager->createQueryBuilder()
                    ->select('m')
                    ->from( 'App\Entity\Movie', 'm' )
                    ->orderBy('m.id', 'ASC')
                    ->getQuery()
                    ->getResult();
        
        return $this->render('dummy_qb/dummyqb1.html.twig', [
            'pelis' => $pelis
        ]);
    }

    #[Route('/dummy/qb2', name: 'dummy_qb2')]
    public function dummy_qb2( EntityManagerInterface $entityManager ): Response
    {
        $pelis = $entityManager->createQueryBuilder()
                    ->select('m')
                    ->from( 'App\Entity\Movie', 'm' )
                    ->where('m.genero = \'Biopic\'')
                    ->andWhere('m.duracion < 120')
                    ->orderBy('m.id', 'ASC')
                    ->getQuery()
                    ->getResult();
        
        return $this->render('dummy_qb/dummyqb2.html.twig', [
            'pelis' => $pelis
        ]);
    }

    #[Route('/dummy/qb3', name: 'dummy_qb3')]
    public function dummy_qb3( EntityManagerInterface $entityManager ): Response
    {
        $pelis = $entityManager->createQueryBuilder()
                    ->select('m')
                    ->from( 'App\Entity\Movie', 'm' )
                    ->where('m.genero = \'Biopic\'')
                    ->orWhere('m.genero = \'Drama\'')
                    ->orderBy('m.id', 'ASC')
                    ->getQuery()
                    ->getResult();
        
        return $this->render('dummy_qb/dummyqb3.html.twig', [
            'pelis' => $pelis
        ]);
    }

    #[Route('/dummy/qb4', name: 'dummy_qb4')]
    public function dummy_qb4( EntityManagerInterface $entityManager ): Response
    {
        $pelis = $entityManager->createQueryBuilder()
                    ->select('m')
                    ->from( 'App\Entity\Movie', 'm' )
                    ->orderBy('m.duracion', 'ASC')
                    ->addOrderBy('m.estreno', 'ASC')
                    ->getQuery()
                    ->getResult();
        
        return $this->render('dummy_qb/dummyqb4.html.twig', [
            'pelis' => $pelis
        ]);
    }

    #[Route('/dummy/qb5/{titulo}', name: 'dummy_qb5')]
    public function dummy_qb5( EntityManagerInterface $entityManager, string $titulo  ): Response
    {
        $pelis = $entityManager->createQueryBuilder()
                    ->select('m')
                    ->from( 'App\Entity\Movie', 'm' )
                    ->where('m.titulo = :titulo')
                    ->setParameter( 'titulo', $titulo)
                    ->getQuery()
                    ->getResult();
        
        return $this->render('dummy_qb/dummyqb5.html.twig', [
            'pelis' => $pelis
        ]);
    }

    #[Route('/dummy/qb6/{titulo}', name: 'dummy_qb6')]
    public function dummy_qb6( EntityManagerInterface $entityManager, string $titulo  ): Response
    {
        $query = $entityManager->createQueryBuilder()
                    ->select('m')
                    ->from( 'App\Entity\Movie', 'm' )
                    ->where('m.titulo LIKE :titulo')
                    ->setParameter( 'titulo', '%'.$titulo.'%')
                    ->getQuery();

        $parameterTitulo = $query->getParameter('titulo');
        $allParameters = $query->getParameters();
        dd($allParameters);

        return $this->render('dummy_qb/dummyqb6.html.twig', [
            'parameterTitulo' => $parameterTitulo,
            'allParameters' => $allParameters
        ]);
    }
}
