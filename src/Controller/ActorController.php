<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Actor;

use App\Form\ActorFormType;
use App\Form\ActorDeleteFormType;

class ActorController extends AbstractController
{
    #[Route('/all_actors', name: 'all_actors')]
    public function index(): Response{

        $actors = $this->getDoctrine()->getRepository( Actor::class )->findAll();
        return $this->render('actor/allactors.html.twig', [
            'actors' => $actors,
        ]);
    }

    #[Route('/actor/create', name: 'actor_create')]
    public function create( Request $request ){
        $actor = new Actor();

        $formulario = $this->createForm( ActorFormType::class, $actor );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist( $actor );
            $entityManager->flush();

            return $this->redirectToRoute('actor_show', [
                'id' => $actor->getId()
            ]);
        }

        return $this->render('actor/create.html.twig', [
            'formulario' => $formulario->createView()
        ]);
    }

    #[Route('/actor/edit/{id}', name: 'actor_edit')]
    public function edit( actor $actor, Request $request ){

        $formulario = $this->createForm( MovieFormType::class, $actor );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Actor actualizada correctamente');

            return $this->redirectToRoute('actor_show', [
                'id' => $actor->getId()
            ]);
        }

        return $this->render('actor/edit.html.twig', [
            'formulario' => $formulario->createView(),
            'actor' => $actor
        ]);
    }

    #[Route('/actor/delete/{id}', name: 'actor_delete')]
    public function delete( Actor $actor, Request $request ): Response {

        $formulario = $this->createForm( MovieDeleteFormType::class, $actor );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actor);
            $entityManager->flush();

            $this->addFlash('success', 'PelicActorula borrada correctamente');

            return $this->redirectToRoute('all_actors');
        }

        return $this->render('actor/delete.html.twig', [
            'formulario'=>$formulario->createView(),
            'actor' => $actor
        ]);
    }

    /**
     * @Route("/actor/show/{id}", name="actor_show")
     * 
     * metodo show hecho de forma "tradicional"
     */
    public function show( Actor $actor ): Response {

        $peli = $this->getDoctrine()->getRepository( Actor::class )->find($actor);

        if(!$peli)
            throw $this->createNotFoundException( "No se encontró el actor con id: $peli." );

        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
        ]); 
    }


}
