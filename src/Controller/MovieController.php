<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Movie;

use App\Form\MovieFormType;
use App\Form\MovieDeleteFormType;
use App\Form\ImageDeleteFormType;
use App\Form\SearchFormType;       
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Filesystem\Filesystem;
use App\Services\FileService;
use App\Services\PaginatorService;
use App\Services\SimpleSearchService;
use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class MovieController extends AbstractController
{
    #[Route('/', name: 'portada')]
    public function portada( EntityManagerInterface $entityManager ): Response
    {           
        $peliculas = $this->getDoctrine()->getRepository( Movie::class )->findAll();

        $pelisLastsWithCovers =  $this->getDoctrine()->getRepository( Movie::class )->findLastsWithCovers(3);
        $pelisMejorValoradas = $this->getDoctrine()->getRepository( Movie::class )->findBetterValoration(5);
        // $movies = $this->getDoctrine()->getRepository( Movie::class )->findByTitle( $valor );

        return $this->render('portada.html.twig', [
            'pelisLastsWithCovers' => $pelisLastsWithCovers,
            'pelisMejorValoradas' => $pelisMejorValoradas
        ]);
    }
    
    
    #[Route('/allmovies/{pagina}', name: 'all_movies', defaults: ['pagina'=> 1], methods: 'GET' )]
    public function allmovies(int $pagina,  PaginatorService $paginator ): Response {

        $paginator->setEntityType('App\Entity\Movie');

        $pelis = $paginator->findAllEntities( $pagina );

        // $movies = $paginator->getPageList();

        // $movies = $this->getDoctrine()->getRepository( Movie::class )->findAll();       //metodo para recuperar las pelis sin paginacion

        return $this->render('movie/allmovies.html.twig', [
            'pelis' => $pelis,
            'totalPaginas' => $paginator->getTotalPages(),
            'totalItems' => $paginator->getTotalItems(),
            'paginaActual' => $pagina,
            'entidad' => 'Películas'
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
    public function create( Request $request, LoggerInterface $appInfoLogger, FileService $uploader ){

        $peli = new Movie();

        $formulario = $this->createForm( MovieFormType::class, $peli );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){
            
            // $file = $request->files->get('movie_form')['caratula'];

            $file = $formulario->get('caratula')->getData();
           
            if( $file ){
                // $extension = $file->guessExtension();
                // $directorio = $this->getParameter('app.covers_root');
                // $fichero = uniqid().".$extension";
                // $file->move($directorio, $fichero);
                // $peli->setCaratula($fichero);
                $uploader->targetDirectory = $this->getParameter('app.covers_root');
                $peli->setCaratula($uploader->upload($file));       // pasamos a tener una sola linea de código en vez de 5 tras implementar el servicio
            }

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
    public function edit( Movie $peli, Request $request, LoggerInterface $appInfoLogger, FileService $uploader ){

        $fichero = $peli->getCaratula();
        
        $formulario = $this->createForm( MovieFormType::class, $peli,  );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            // $file = $request->files->get('movie_form')['caratula'];
            $file = $formulario->get('caratula')->getData();

            if($file){
                // $directorio = $this->getParameter('app.covers_root');

                // if($fichero){
                //     $filesystem = new Filesystem();
                //     $filesystem->remove("$directorio/$fichero");
                // }

                // $extension = $file->guessExtension();
                // $fichero = uniqid()."$extension"; 
                // $file->move( $directorio, $fichero );
                $uploader->targetDirectory = $this->getParameter('app.covers_root');
                $fichero = $uploader->replace( $file, $fichero );     
                $this->addFlash( 'success', 'Imagen actualizada correctamente' );
                // $this->addFlash( 'success', 'Imagenes reemplazadas correctamente' );
                // probado con exito el borrado del fichero: 618a94c1cef64.jpg
            }

            $peli->setCaratula($fichero);

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
    public function delete( Movie $peli, Request $request, LoggerInterface $appInfoLogger, FileService $uploader ): Response {

        $formulario = $this->createForm( MovieDeleteFormType::class, $peli );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            if( $peli->getCaratula() ){
                // $directorio = $this->getParameter('app.covers_root');
                // $filesystem = new Filesystem(); 
                // $filesystem->remove($directorio.'/'.$peli->getCaratula());
                $uploader->targetDirectory = $this->getParameter('app.covers_root');

                if( $uploader->remove( $peli->getCaratula())){
                    $this->addFlash( 'success', 'Imagen borrada correctamente' );
                }else{
                    $this->addFlash( 'warning', 'Imagen no borrada' );
                }
            }

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

    // #[Route('/movie/search', name: 'movie_search', methods: 'POST' )]
    // public function search( Request $request, LoggerInterface $appSearchLogger ): Response {
        
    //     $valor = $request->request->get('valor');

    //     $movies = $this->getDoctrine()->getRepository( Movie::class )->findByTitle( $valor );
        
    //     // $movies->findByTitle( $valor );

    //     $appSearchLogger->info( "Se ha buscado el término: ".$valor );

    //     return $this->render('movie/allmovies.html.twig', [
    //         'movies' => $movies,
    //         'totalPaginas' => 5,
    //         'totalItems' => 5,
    //         'paginaActual' => 1,
    //     ]);
    // }
    #[Route('/movie/search', name: 'movie_search', methods: ['GET', 'POST'] )]
    public function search ( Request $request, SimpleSearchService $busqueda ): Response {

        $formulario = $this->createForm( SearchFormType::class, $busqueda, [
            'field_choices' => [
                'Titulo' => 'titulo',
                'Director' => 'director',
                'Genero' => 'genero',
                'Sinopsis' => 'sinopsis'
            ],
            'order_choices' => [
                'Id' => 'id',
                'Titulo' => 'titulo',
                'Director' => 'director',
                'Genero' => 'genero',
            ]
        ]);

        $formulario->get('valor')->setData($busqueda->campo);
        $formulario->get('orden')->setData($busqueda->orden);

        $formulario->handleRequest( $request );

        $pelis = $busqueda->search( 'App\Entity\Movie');

        // $valor = $request->request->get('valor');
        // // $valor = $formulario->get('valor')->setData($busqueda->valor);
        // dd($request);
        // $appSearchLogger->info( "Se ha buscado el término: ".$valor);

        return $this->renderForm('movie/searchform.html.twig', [
            'formulario' => $formulario,
            'pelis' => $pelis
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

    #[Route('/movie/deleteimage/{id}', name: 'movie_delete_image', methods:"GET")]
    public function deleteimage( Movie $peli, Request $request, LoggerInterface $appInfoLogger, FileService $uploader ): Response {
   
        if($peli->getCaratula()){

            // $filesystem = new Filesystem();
            // $directorio = $this->getParameter('app.covers_root');
            $uploader->targetDirectory = $this->getParameter('app.covers_root');
            $uploader->remove( $peli->getCaratula() );

            if( $uploader->remove( $peli->getCaratula())){
                $this->addFlash( 'success', 'Imagen borrada correctamente' );
            }else{
                $this->addFlash( 'warning', 'Imagen no borrada' );
            }

            // $filesystem->remove($directorio.'/'.$peli->getCaratula());
            $peli->setCaratula(NULL);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $mensaje = "Carátula de la Pelicula ".$peli->getTitulo()." borrada correctamente";
            $this->addFlash( 'success', $mensaje );
            $appInfoLogger->info( $mensaje );

            return $this->redirectToRoute('movie_edit', ['id'=>$peli->getId()]);

        }

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

    // /**
    //  * @Route("/searchlogs", name="searchlogs")
    //  */
    // public function searchlogs(): Response {

    //     // "[2021-11-03T15:22:50.601744+00:00] app_search.INFO: Se ha buscado el término: Peter [] []" //linea que es guardada en el log de search
    //     $logs = file('..\var\log\appsearch.log');
    //     $resultado = [];

    //     foreach( $logs as $log ){
    //         $logLimpio = str_replace('[]', ' ', $log);
    //         $terminoBusqueda = substr( $logLimpio, 1, 10);
    //         $terminoBusqueda .= ': '.substr( $logLimpio, 79, (strlen($logLimpio)) );
    //         array_push( $resultado, $terminoBusqueda );

    //         //TODO contar el numero de busquedas de caada término y añadirlo a la vista
    //     }

    //     return $this->render('movie/searchlog.html.twig', [
    //         'resultado' => $resultado,
    //     ]);

    // }


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
