<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Movie;

class MovieController extends AbstractController
{
    #[Route('/allmovies', name: 'all_movies')]
    public function allmovies(): Response {

        $movies = $this->getDoctrine()->getRepository( Movie::class )->findAll();
        return $this->render('movie/allmovies.html.twig', [
            'controller_name' => 'MovieController',
            'movies' => $movies,
        ]);
    }

    #[Route('/movie/store', name: 'movie_store')]
    public function store(){
        //gestor de entidades
        $entityManager = $this->getDoctrine()->getManager();

        $peli = new Movie();

        $peli
            ->setTitulo('Some Kind of Monster')
            ->setDirector('Joe Berlinger & Bruce Sinofsky')
            ->setDuracion(103)
            ->setGenero('Documentary');
        
        $entityManager->persist($peli);
        $entityManager->flush();

        return new Response('Pelicula guardada en la DB con id: '.$peli->getId()
                            .' con un tipo: '.gettype($peli->getId()));
    }

    /**
     * @Route("/movie/show/{id}", name="movie_show")
     * 
     * metodo show hecho de forma "tradicional"
     */
    public function show( Movie $movie ): Response {

        $peli = $this->getDoctrine()->getRepository( Movie::class )->find($movie);

        if(!$peli)
            throw $this->createNotFoundException( "No se encontró la película con id: $peli." );

        return $this->render('movie/show.html.twig', [
            'peli' => $peli,
        ]); 
    }

     /**
     * @Route("/movie/{id}", name="movie_show_id")
     * 
     * metodo show hecho a la Doctrine way
     */
    public function mostrar( Movie $movie ): Response {
        
        return new Response(" Información 'Doctrine way': $movie");

    }

    /**
     * @Route("/movies", name="movies")
     */
    public function index(): Response {

        $pelis = $this->getDoctrine()->getRepository( Movie::class )->findAll();

        return new Response ("Listado de películas en la DB: <br> ".implode("<br>", $pelis));

    }

    /**
     * @Route("/movie/search/{campo}/{valor}", name="movie_search")
     */
    public function search( string $campo, string $valor ): Response {
        $criterio = [ $campo=>$valor ];

        $pelis = $this->getDoctrine()->getRepository( Movie::class )->findBy( $criterio );
        
        return new Response( "Lista de peliculas encontradas con los criterios CAMPO: $campo y VALOR: $valor:<br>"
                            .implode("<br>", $pelis));
    }

    /**
     * @Route("/movie/update/{id}", name="movie_update")
     */
    public function update( Movie $movie ): Response {
        if($movie->getId() > 10 ){
            $entityManager= $this->getDoctrine()->getManager();

            $movie->setTitulo('RockAndRollCircus');
            $entityManager->flush();

            return $this->redirectToRoute('movie_show', ['id'=> $movie->getId()]);

        }else{
            return $this->redirectToRoute('movies');
        } 
    }

    /**
     * @Route("/movie/destroy/{id}", name="movie_delete")
     */
    public function destroy ( Movie $movie ){
        if($movie->getId() >10 ){
            $entityManager= $this->getDoctrine()->getManager();
            
            $entityManager->remove( $movie );
            $entityManager->flush();

            return new Response ( "Borrado de la pelicula $movie " );

        }else{
            return $this->redirectToRoute('movies');
        }
    }
}
