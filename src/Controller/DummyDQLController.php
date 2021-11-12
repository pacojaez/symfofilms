<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DummyDQLController extends AbstractController
{
    #[Route('/dummy/dql', name: 'dummy_dql')]
    public function index(): Response {
        return $this->render('dummy_dql/index.html.twig', [
            'controller_name' => 'DummyDQLController',
        ]);
    }

    #[Route('/dummy/dql1', name: 'dummy_dql1')]
    public function dql1( EntityManagerInterface $entityManager ): Response {
        $pelis = $entityManager->createQuery(
            "SELECT m
            FROM App\Entity\Movie m
            WHERE m.valoracion > 3
            ORDER BY m.valoracion DESC"
        )
        ->getResult();
        
        return $this->render('dummy_dql/dql1.html.twig', [
            'pelis' => $pelis
        ]);
    }

    #[Route('/dummy/dql2', name: 'dummy_dql2')]
    public function dql2( EntityManagerInterface $entityManager ): Response {
        $pelis = $entityManager->createQuery(
            "SELECT m.titulo, m.estreno AS year
            FROM App\Entity\Movie m"
        )
        ->getResult();
        
        return $this->render('dummy_dql/dql2.html.twig', [
            'pelis' => $pelis
        ]);
    }

    #[Route('/dummy/dql3', name: 'dummy_dql3')]
    public function dql3( EntityManagerInterface $entityManager ): Response {
        $pelis = $entityManager->createQuery(
            "SELECT m
            FROM App\Entity\Movie m
            ORDER BY m.id ASC"
        )
        ->setFirstResult(0)
        ->setMaxResults(5)
        ->getResult();
        
        return $this->render('dummy_dql/dql3.html.twig', [
            'pelis' => $pelis
        ]);
    }

    #[Route('/dummy/dql4', name: 'dummy_dql4')]
    public function dql4( EntityManagerInterface $entityManager ): Response {
        $pelis = $entityManager->createQuery(
            "SELECT m
            FROM App\Entity\Movie m
            WHERE m.valoracion <= 3 AND m.duracion > 120
            ORDER BY m.id ASC"
        )
        ->getResult();
        
        return $this->render('dummy_dql/dql4.html.twig', [
            'pelis' => $pelis
        ]);
    }

    #[Route('/dummy/dql5', name: 'dummy_dql5')]
    public function dql5( EntityManagerInterface $entityManager ): Response {
        $pelis = $entityManager->createQuery(
            "SELECT m
            FROM App\Entity\Movie m
            WHERE m.valoracion BETWEEN 2 AND 3
            ORDER BY m.id ASC"
        )
        ->getResult();
        
        return $this->render('dummy_dql/dql5.html.twig', [
            'pelis' => $pelis
        ]);
    }

    #[Route('/dummy/dql6', name: 'dummy_dql6')]
    public function dql6 ( EntityManagerInterface $entityManager ): Response {
        $actors = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Actor a
            WHERE a.fechanacimiento 
            BETWEEN \'1960-03-31\' AND \'1990-03-31\' '
        )
        ->getResult();
        
        return $this->render('dummy_dql/dql6.html.twig', [
            'actors' => $actors
        ]);
    }

    #[Route('/dummy/dql7', name: 'dummy_dql7')]
    public function dql7 ( EntityManagerInterface $entityManager ): Response {
        $actors = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Actor a
            WHERE a.nacionalidad 
            IN (\'canadiense\' , \'belga\' )'
        )
        ->getResult();
        
        return $this->render('dummy_dql/dql7.html.twig', [
            'actors' => $actors
        ]);
    }

    #[Route('/dummy/dql8', name: 'dummy_dql8')]
    public function dql8 ( EntityManagerInterface $entityManager ): Response {
        $actors = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Actor a
            WHERE a.nombre
            LIKE \'%mari%\' 
            '
        )
        ->getResult();
        
        return $this->render('dummy_dql/dql8.html.twig', [
            'actors' => $actors
        ]);
    }

    #[Route('/dummy/dql10', name: 'dummy_dql10')]
    public function dql10 ( EntityManagerInterface $entityManager ): Response {
        $movies = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Movie m
            WHERE m.sinopsis IS NULL
            ORDER BY m.id ASC
            '
        )
        ->getResult();
        
        return $this->render('dummy_dql/dql10.html.twig', [
            'movies' => $movies
        ]);
    }

    #[Route('/dummy/dql11/{titulo}', name: 'dummy_dql11', methods:"GET", defaults: ['titulo'=> 'Rocketman'])]
    public function dql11 ( string $titulo,  EntityManagerInterface $entityManager ): Response {
        
        $movies = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Movie m
            WHERE m.titulo LIKE :titulo')
            ->setParameter('titulo',  "%$titulo%")
            ->getResult();

        return $this->render('dummy_dql/dql11.html.twig', [
            'movies' => $movies
        ]);
    }

    #[Route('/dummy/dql12', name: 'dummy_dql12' )]
    public function dql12 ( EntityManagerInterface $entityManager ): Response {
        
        $generos = $entityManager->createQuery(
            'SELECT DISTINCT m.genero
            FROM App\Entity\Movie m
            ')
            ->getResult();
        
        return $this->render('dummy_dql/dql12.html.twig', [
            'generos' => $generos
        ]);
    }

    #[Route('/dummy/dql13', name: 'dummy_dql13' )]
    public function dql13 ( EntityManagerInterface $entityManager ): Response {
        
        $numeroGeneros = $entityManager->createQuery(
            'SELECT COUNT(DISTINCT m.genero)
            FROM App\Entity\Movie m
            ')
            ->getSingleScalarResult();

        return $this->render('dummy_dql/dql13.html.twig', [
            'numeroGeneros' => $numeroGeneros
        ]);
    }

    #[Route('/dummy/dql14', name: 'dummy_dql14' )]
    public function dql14 ( EntityManagerInterface $entityManager ): Response {
        
        $movies = $entityManager->createQuery(
            'SELECT m.titulo, m.valoracion*20 AS sobreCien
            FROM App\Entity\Movie m
            ORDER BY sobreCien DESC, m.titulo ASC
            ')
            ->getResult();

        return $this->render('dummy_dql/dql14.html.twig', [
            'movies' => $movies
        ]);
    }

    #[Route('/dummy/dql15', name: 'dummy_dql15' )]
    public function dql15 ( EntityManagerInterface $entityManager ): Response {
        
        $actors = $entityManager->createQuery(
            'SELECT a.nombre, DATE_DIFF(CURRENT_DATE(), a.fechanacimiento)/365 AS edad
            FROM App\Entity\Actor a
            ORDER BY edad DESC
            ')
            ->getResult();

        return $this->render('dummy_dql/dql15.html.twig', [
            'actors' => $actors
        ]);
    }

    #[Route('/dummy/dql16', name: 'dummy_dql16' )]
    public function dql16 ( EntityManagerInterface $entityManager ): Response {
        
        $actors = $entityManager->createQuery(
            'SELECT a.nombre, YEAR(CURRENT_DATE())-YEAR(a.fechanacimiento) AS edad
            FROM App\Entity\Actor a
            ORDER BY edad DESC
            ')
            ->getResult();

        return $this->render('dummy_dql/dql16.html.twig', [
            'actors' => $actors
        ]);
    }



}
