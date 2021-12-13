<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use App\Entity\Movie;

#[Route('/api', name: 'api_')]
class ApiMovieController extends AbstractController {

    #[Route('/movies/{formato}', name: 'allMovies', requirements: ['formato'=>'json|csv|xml'], defaults: ['formato'=>'json'])]
    public function allmovies( string $formato ): Response {

        $movies = $this->getDoctrine()->getRepository( Movie::class )->findAll();
        // dd($movies);
        $serializer = new Serializer([new ObjectNormalizer()],
                     [ new JsonEncoder(), new CsvEncoder(), new XmlEncoder()]);
                     
        $formato = strtolower($formato);
        
        // falla: A circular reference has been detected when serializing the object of class "App\Entity\Movie"
        $contenido = $serializer->serialize( $movies, $formato, [ ObjectNormalizer::IGNORED_ATTRIBUTES => ['user', 'actors', 'comments'] ]);        
        // dd($contenido);
        $response = new Response( $contenido );

        switch ( $formato ){
            case 'json' : $formato = 'application/json'; break;
            case 'csv' : $formato = 'text/csv'; break;
            case 'xml' : $formato = 'text/xml'; break;
        }

        $response->headers->set('Content-Type', $formato );

        return $response;
    }

    #[Route('/movies/{id}/{formato}', name: 'oneMovie', requirements: ['formato'=>'json|csv|xml'], defaults: ['formato'=>'json'])]
    public function onemovie( int $id,  string $formato ): Response {

        $movies = $this->getDoctrine()->getRepository( Movie::class )->find($id);
        // dd($movies);
        $serializer = new Serializer([new ObjectNormalizer()],
                     [ new JsonEncoder(), new CsvEncoder(), new XmlEncoder()]);
                     
        $formato = strtolower($formato);
        
        // falla: A circular reference has been detected when serializing the object of class "App\Entity\Movie"
        $contenido = $serializer->serialize( $movies, $formato, [ ObjectNormalizer::IGNORED_ATTRIBUTES => ['user', 'actors', 'comments'] ]);        
        // dd($contenido);
        $response = new Response( $contenido );

        switch ( $formato ){
            case 'json' : $formato = 'application/json'; break;
            case 'csv' : $formato = 'text/csv'; break;
            case 'xml' : $formato = 'text/xml'; break;
        }

        $response->headers->set('Content-Type', $formato );

        return $response;
    }
}
