<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\MovieRepository;

use App\Services\SimpleSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

#[Route('/api', name: 'api_')]
class ApiMovieController extends AbstractController {

    //Propiedades
    private $movieRepository;
    private $serializer;

    // MIME types
    private $cabeceras = [
        "json" => "application/json",
        "xml" => "text/xml"
    ];

    //Constructor
    public function __construct (MovieRepository $movieRepository){
        $this->movieRepository = $movieRepository;

        $this->serializer = new Serializer([new ObjectNormalizer()],
                            [ new JsonEncoder(), new CsvEncoder(), new XmlEncoder()]);
    }

    /**
     * metodo para enviar las repuestas
     *
     * @param string $contenido
     * @param string $formato
     * @param [type] $codigo
     * @return Response
     */
    public function sendResponse ( string $contenido = '', string $formato = 'json', int $codigo = Response::HTTP_OK ): Response{
        //crea la respuesta
        $response = new Response ($contenido, $codigo);
        $response->headers->set( 'Content-Type', $this->cabeceras[strtolower($formato)]);

        return $response;
    }

    
    #[Route('/movies/{formato}', name: 'allMovies', requirements: ['formato'=>'json|csv|xml'], defaults: ['formato'=>'json'], methods: ['GET'])]
    /**
     * Returns a response with the format 
     *
     * @param string $formato
     * @return void
     */
    public function allmovies( string $formato ): Response {

        $movies = $this->movieRepository->findAll();
        
        //moving this to a class method i dont repeat the serializer
        // $serializer = new Serializer([new ObjectNormalizer()],
        //              [ new JsonEncoder(), new CsvEncoder(), new XmlEncoder()]);                
        // $formato = strtolower($formato);

        // fallaba: A circular reference has been detected when serializing the object of class "App\Entity\Movie"
        // Added status and data for a better repsonse
        $contenido = $this->serializer->serialize( [
                                        "status" =>  "OK",
                                        "data" => $movies
                                    ], 
                                        $formato, 
                                        [ ObjectNormalizer::IGNORED_ATTRIBUTES => ['user', 'actors', 'comments'] ]);
                                        
        // moved this to a public class method so the code became more readble
        // $response = new Response( $contenido );
        // switch ( $formato ){
        //     case 'json' : $formato = 'application/json'; break;
        //     case 'csv' : $formato = 'text/csv'; break;
        //     case 'xml' : $formato = 'text/xml'; break;
        // }
        // $response->headers->set('Content-Type', $formato );
        // return $response;

        return $this->sendResponse($contenido, $formato);
    }

    #[Route('/movies/{id<\d+>}/{formato}', name: 'oneMovie', requirements: ['formato'=>'json|csv|xml'], defaults: ['formato'=>'json'], methods: ['GET'])]
    /**
     * Returns one movie in the specified format
     *
     * @param integer $id
     * @param string $formato
     * @return Response
     */
    public function onemovie( int $id,  string $formato ): Response {

        $movie = $this->movieRepository->find($id);
        // $serializer = new Serializer([new ObjectNormalizer()],
        //              [ new JsonEncoder(), new CsvEncoder(), new XmlEncoder()]);
                     
        // $formato = strtolower($formato);

        $contenido = $this->serializer->serialize(
                    $movie? [ 
                            'status' => 'OK', 
                            'data' => $movie
                            ] : 
                            [
                                'status' => "ERROR",
                                'message' => "No se ha encontrado la pelicula con id $id",
                            ],
                    $formato,
                    [ ObjectNormalizer::IGNORED_ATTRIBUTES => ['movies', 'actors', 'comments'] ]
                );
                    
        // dd($user);
        // fallaba: A circular reference has been detected when serializing the object of class "App\Entity\Movie"
        // $contenido = $serializer->serialize( [
        //                             "status" =>  "OK",
        //                             "data" => $movie
        //                         ], 
        //                         $formato, [ ObjectNormalizer::IGNORED_ATTRIBUTES => ['user', 'actors', 'comments'] ]);        
        // dd($contenido);
        // $response = new Response( $contenido );
        // switch ( $formato ){
        //     case 'json' : $formato = 'application/json'; break;
        //     case 'csv' : $formato = 'text/csv'; break;
        //     case 'xml' : $formato = 'text/xml'; break;
        // }
        // $response->headers->set('Content-Type', $formato );
        // return $response;

        return $this->sendResponse (
            $contenido,
            $formato,
            $movie ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }


    #[Route('/movies/search/{campo}/{valor}/{formato}', 
                name: 'api_movie_search', 
                requirements: ['formato'=>'json|csv|xml'], 
                defaults: [
                    'campo' => 'title',
                    'valor' => "%",
                    'formato'=>'json',
                    ], 
                methods: ['GET']
            )]
    /**
     * search in the api
     *
     * @param string $campo
     * @param string $valor
     * @param string $formato
     * @param SimpleSearchService $searchService
     * @return Response
     */
    public function search ( string $campo, string $valor, string $formato, SimpleSearchService $searchService ): Response {
        $searchService->campo = $campo;
        $searchService->valor = $valor;
        $movies = $searchService->search('\App\Entity\Movie');

        // normalizar y serializar la respuesta
        $contenido = $this->serializer->serialize( [
            "status" =>  "OK",
            "data" => $movies
        ], 
            $formato, 
            [ ObjectNormalizer::IGNORED_ATTRIBUTES => ['user', 'actors', 'comments'] ]);

        //devolvemos la respuesta    
        return $this->sendResponse($contenido, $formato);
    }

    #[Route('/movies/{formato}', 
                name: 'api_movie_create', 
                requirements: ['formato'=>'json|csv|xml'], 
                defaults: [
                    'formato'=>'json',
                    ], 
                methods: ['POST']
            )]
    /**
     * create a movie via the API
     *
     * @param string $formato
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function create ( string $formato, Request $request, EntityManagerInterface $em, ValidatorInterface $validator ): Response {

        try{
            $movie = $this->serializer->deserialize(
                $request->getContent(),
                'App\Entity\Movie',
                $formato
            );
        }catch ( NotEncodableValueException $e ){
            $contenido = $this->serializer->serialize([
                "status" => "ERROR",
                "message" => "Error en el $formato. Se ha recibido: ".$request->getContent()
            ], $formato);

            return $this->sendResponse ($contenido, $formato, Response::HTTP_BAD_REQUEST );
        }

        $errors = $validator->validate($movie);

        if(count( $errors) > 0 ){
            $errores = [];

            foreach( $errors as $error )
                $errores[ $error->getPropertyPath()] = $error->getMessage();

            $contenido = $this->serializer->serialize([
                "status" => "ERROR",
                "message" => "Error de validación",
                "errors" => $errores
            ], $formato );

            return $this->sendResponse( $contenido, $formato, Response::HTTP_UNPROCESSABLE_ENTITY );

        }else{

            // Para probar asignamos el id 25 de un usuario,
            // deberemos autenticar primero al usuario via API
            // y añadirlo a la $movie
            $movie->setUser ($this->getDoctrine()->getRepository( User::class )->find(25));
            
            $em->persist($movie);
            $em->flush();
            $contenido = $this->serializer->serialize([
                "status" => "OK",
                "message" => "Pelicula guardada con id: ".$movie->getId()
            ], $formato);

            return $this->sendResponse($contenido, $formato, Response::HTTP_CREATED);
        }
            

    }


    #[Route('/movies/{id<\d+>}/{formato}', 
                name: 'api_movie_edit', 
                requirements: ['formato'=>'json|csv|xml'], 
                defaults: [
                    'formato'=>'json',
                    ], 
                methods: ['PUT', 'PATCH']
            )]
    /**
     * edit a movie from the API
     *
     * @param integer $id
     * @param string $formato
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function edit( int $id, string $formato, Request $request, EntityManagerInterface $em, ValidatorInterface $validator ) : Response {


        $movie = $this->movieRepository->find($id);
        if( !$movie){
            $contenido = $this->serializer->serialize([
                "status" => "ERROR",
                "message" => "No hay ninguna pelicula con el id: $id",
            ], $formato );

            return $this->sendResponse( $contenido, $formato, Response::HTTP_NOT_FOUND);
        }

        try{
            $this->serializer->deserialize(
                $request->getContent(),
                'App\Entity\Movie', $formato,
                ['object_to_populate' => $movie]
            );
        }catch ( NotEncodableValueException $e ){
            $contenido = $this->serializer->serialize([
                "status" => "ERROR",
                "message" => "Error en el $formato. Se ha recibido: ".$request->getContent()
            ], $formato);

            return $this->sendResponse ($contenido, $formato, Response::HTTP_BAD_REQUEST );
        }

        $errors = $validator->validate($movie);

        if(count( $errors) > 0 ){
            $errores = [];

            foreach( $errors as $error )
                $errores[ $error->getPropertyPath()] = $error->getMessage();

            $contenido = $this->serializer->serialize([
                "status" => "ERROR",
                "message" => "Error de validación",
                "errors" => $errores
            ], $formato );

            return $this->sendResponse( $contenido, $formato, Response::HTTP_UNPROCESSABLE_ENTITY );

        }else{

            // Para probar asignamos el id 25 de un usuario,
            // deberemos autenticar primero al usuario via API
            // y añadirlo a la $movie
            $movie->setUser ($this->getDoctrine()->getRepository( User::class )->find(25));
            
            // $em->persist($movie);
            $em->flush();
            $contenido = $this->serializer->serialize([
                "status" => "OK",
                "message" => "Pelicula con id: ".$movie->getId()." actualizada correctamente.",
            ], $formato);

            return $this->sendResponse($contenido, $formato, Response::HTTP_CREATED);
        }
        
    }

    #[Route('/movies/{id<\d+>}/{formato}', 
    name: 'api_movie_delete', 
    requirements: ['formato'=>'json|csv|xml'], 
    defaults: [
        'formato'=>'json',
        ], 
    methods: ['DELETE']
    )]
    /**
    * delete a movie from the API
    *
    * @param integer $id
    * @param string $formato
    * @param EntityManagerInterface $em
    * @return Response
    */
    public function delete( int $id, string $formato, EntityManagerInterface $em) : Response {
        $movie = $this->movieRepository->find($id);

        if($movie){
            $id = $movie->getId();
            $em->remove($movie);
            $em->flush();

            $contenido = $this->serializer->serialize([
                "status" => "OK",
                "message" => "Pelicula con id ". $id." borrada correctamente.",
            ], $formato);

            return $this->sendResponse($contenido, $formato );
        }

        $contenido = $this->serializer->serialize([
            "status" => "ERROR",
            "message" => "No hay ninguna pelicula con el id: $id",
        ], $formato );

        return $this->sendResponse( $contenido, $formato, Response::HTTP_NOT_FOUND);
    }
}
