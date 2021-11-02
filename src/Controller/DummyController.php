<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DummyController extends AbstractController
{
    // #[Route('/dummy', name: 'dummy', methods: ["GET", "POST"] )]
    // public function index(): Response
    // {
        
        
    //     return $this->render('dummy/index.html.twig', [
    //         'controller_name' => 'DummyController',
    //     ]);
    //     // return new Response ("Esto es una prueba de rutas desde DummyController con anotacion  #[Route('/dummy', name: 'dummy')]");
    // }

    /**
     * @Route("/dummy/foo/{nombre?<\w+>}/{id?}", name="dummy_nombre")
     */
    // public function nombre( ?string $nombre, ?int $id ): Response
    // {
    //     return $this->render('dummy/index.html.twig', [
    //         'controller_name' => 'DummyController',
    //         'nombre' => $nombre,
    //         'id' => $id
    //     ]);
    //     // return new Response ("Esto es una prueba de rutas desde DummyController con anotacion  #[Route('/dummy', name: 'dummy')]");
    // }

    /**
     *@Route("/dummy/id/{id<\d+>}", name="dummy_id")
     */
    // public function id( int $id ): Response
    // {
    //     return $this->render('dummy/index.html.twig', [
    //         'controller_name' => 'DummyController',
    //         'id' => $id,
    //         'nombre'=> 'Paco'
    //     ]);
    //     // return new Response ("Esto es una prueba de rutas desde DummyController con anotacion  #[Route('/dummy', name: 'dummy')]");
    // }

    /**
     *@Route("/dummy/firefox", name="dummy_firefox",
     *         condition="request.headers.get('User-Agent') matches '/firefox/i'")
     */
    public function firefox( Request $request ): Response {
        dd($request);
        return new Response ("Estas en firefox!!");
    }

    /**
     *@Route("/dummy/edge", name="dummy_edge",
     *         condition="request.headers.get('User-Agent') matches '/edg/i'")
     */
    public function edge( Request $request ): Response {
        dd($request);
        return new Response ("Estas en edge!!");
    }
}
