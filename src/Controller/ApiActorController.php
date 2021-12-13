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

use App\Entity\Actor;

#[Route('/api', name: 'api_')]
class ApiActorController extends AbstractController
{
    #[Route('/actors/{formato}', name: 'allActors', requirements: ['formato'=>'json|csv|xml'], defaults: ['formato'=>'json'])]
    public function allactors( string $formato ): Response{
        
        $actors = $this->getDoctrine()->getRepository( Actor::class )->findAll();

        $serializer = new Serializer([new ObjectNormalizer()],
                     [ new JsonEncoder(), new CsvEncoder(), new XmlEncoder()]);
        
        $formato = strtolower($formato);
        $contenido = $serializer->serialize( $actors, $formato, [ ObjectNormalizer::IGNORED_ATTRIBUTES => ['user', 'movies', 'fechaNacimiento'] ] );
        
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
