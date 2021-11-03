<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Movie;

use App\Form\MovieFormType;
use App\Form\MovieDeleteFormType;
use Symfony\Component\HttpFoundation\Request;

use Psr\Log\LoggerInterface;

class MovieController extends AbstractController
{
    #[Route('/allmovies', name: 'all_movies')]
    public function allmovies(): Response {

        $movies = $this->getDoctrine()->getRepository( Movie::class )->findAll();
        return $this->render('movie/allmovies.html.twig', [
            'movies' => $movies,
        ]);
    }

    // #[Route('/movie/store', name: 'movie_store')]
    // public function store(){
    //     //gestor de entidades
    //     $entityManager = $this->getDoctrine()->getManager();

    //     $peli = new Movie();

    //     $peli
    //         ->setTitulo('Some Kind of Monster')
    //         ->setDirector('Joe Berlinger & Bruce Sinofsky')
    //         ->setDuracion(103)
    //         ->setGenero('Documentary');
        
    //     $entityManager->persist($peli);
    //     $entityManager->flush();

    //     return new Response('Pelicula guardada en la DB con id: '.$peli->getId()
    //                         .' con un tipo: '.gettype($peli->getId()));
    // }

    #[Route('/movie/create', name: 'movie_create')]
    public function create( Request $request, LoggerInterface $appInfoLogger ){
        $peli = new Movie();

        $formulario = $this->createForm( MovieFormType::class, $peli );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist( $peli );
            $entityManager->flush();

            $mensaje = "Pelicula ".$peli->getTitulo()." con id: ".$peli->getId()." guardada correctamente";
            $this->addFlash( 'success', $mensaje );
            $appInfoLogger->info( $mensaje );

            return $this->redirectToRoute('movie_show', [
                'id' => $peli->getId()
            ]);
        }

        return $this->render('movie/create.html.twig', [
            'formulario' => $formulario->createView()
        ]);
    }

    #[Route('/movie/edit/{id}', name: 'movie_edit')]
    public function edit( Movie $peli, Request $request, LoggerInterface $appInfoLogger ){

        $formulario = $this->createForm( MovieFormType::class, $peli );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $mensaje = "Pelicula ".$peli->getTitulo()." con id: ".$peli->getId()." actualizada correctamente";
            $this->addFlash( 'success', $mensaje );
            $appInfoLogger->info( $mensaje );

            return $this->redirectToRoute('movie_show', [
                'id' => $peli->getId()
            ]);
        }

        return $this->render('movie/edit.html.twig', [
            'formulario' => $formulario->createView(),
            'peli' => $peli
        ]);
    }

    #[Route('/movie/delete/{id}', name: 'movie_delete')]
    public function delete( Movie $peli, Request $request, LoggerInterface $appInfoLogger ): Response {

        $formulario = $this->createForm( MovieDeleteFormType::class, $peli );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($peli);
            $entityManager->flush();

            $mensaje = "Pelicula ".$peli->getTitulo()." borrada correctamente";
            $this->addFlash( 'success', $mensaje );
            $appInfoLogger->info( $mensaje );

            return $this->redirectToRoute('all_movies');
        }

        return $this->render('movie/delete.html.twig', [
            'formulario'=>$formulario->createView(),
            'peli' => $peli
        ]);
    }

     /**
     * @Route("/movie/search", name="movie_search")
     */
    public function search( Request $request, LoggerInterface $appSearchLogger ): Response {
        
        $valor = $request->request->get('valor');

        $movies = $this->getDoctrine()->getRepository( Movie::class )->findByTitle( $valor );

        $appSearchLogger->info( "Se ha buscado el término: ".$valor );

        return $this->render('movie/allmovies.html.twig', [
            'movies' => $movies,
        ]);
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
     * @Route("/searchlogs", name="search_logs")
     */
    public function searchlogs(): Response {

        // "[2021-11-03T15:22:50.601744+00:00] app_search.INFO: Se ha buscado el término: Peter [] []" //linea que es guardada en el log de search
        $logs = file('..\var\log\appsearch.log');
        $resultado = [];

        foreach( $logs as $log ){
            $logLimpio = str_replace('[]', ' ', $log);
            $terminoBusqueda = substr( $logLimpio, 1, 10);
            $terminoBusqueda .= ': '.substr( $logLimpio, 79, (strlen($logLimpio)) );
            array_push( $resultado, $terminoBusqueda );

            //TODO contar el numero de busquedas de caada término y añadirlo a la vista
        }

        return $this->render('movie/searchlog.html.twig', [
            'resultado' => $resultado,
        ]);

    }


    /**
     * @Route("/movie/update/{id}", name="movie_update")
     */
    // public function update( Movie $movie ): Response {
    //     if($movie->getId() > 10 ){
    //         $entityManager= $this->getDoctrine()->getManager();

    //         $movie->setTitulo('RockAndRollCircus');
    //         $entityManager->flush();

    //         return $this->redirectToRoute('movie_show', ['id'=> $movie->getId()]);

    //     }else{
    //         return $this->redirectToRoute('movies');
    //     } 
    // }

    /**
     * @Route("/movie/destroy/{id}", name="movie_delete")
     */
    // public function destroy ( Movie $movie ){
    //     if($movie->getId() >10 ){
    //         $entityManager= $this->getDoctrine()->getManager();
            
    //         $entityManager->remove( $movie );
    //         $entityManager->flush();

    //         return new Response ( "Borrado de la pelicula $movie " );

    //     }else{
    //         return $this->redirectToRoute('movies');
    //     }
    // }
}
