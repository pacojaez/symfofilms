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

            $this->addFlash('success', 'Actor creado correctamente');

            return $this->redirectToRoute('actor_show', [
                'id' => $actor->getId()
            ]);
        }

        return $this->render('actor/create.html.twig', [
            'formulario' => $formulario->createView()
        ]);
    }

    #[Route('/actor/edit/{id}', name: 'actor_edit')]
    public function edit( Actor $actor, Request $request ){

        $formulario = $this->createForm( ActorFormType::class, $actor );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

       //calculo de la edad del actor:
       $edad = $this->calculoEdad($actor->getFechaNacimiento());
        
        
        if($formulario->isSubmitted() && $formulario->isValid()){

            //calculo de la edad del actor:
            $edad = $this->calculoEdad($actor->getFechaNacimiento());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Actor actualizado correctamente');

            return $this->redirectToRoute('actor_show', [
                'id' => $actor->getId(),
                'edad' => $edad
            ]);
        }

        return $this->render('actor/edit.html.twig', [
            'formulario' => $formulario->createView(),
            'actor' => $actor,
            'edad' => $edad
        ]);
    }

    #[Route('/actor/delete/{id}', name: 'actor_delete')]
    public function delete( Actor $actor, Request $request ): Response {

        $formulario = $this->createForm( ActorDeleteFormType::class, $actor );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actor);
            $entityManager->flush();

            $this->addFlash('success', 'Actor borrada correctamente');

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

        //calculo de la edad del actor:
       $edad = $this->calculoEdad($actor->getFechaNacimiento());

        if(!$peli)
            throw $this->createNotFoundException( "No se encontró el actor con id: $peli." );

        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
            'edad' => $edad
        ]); 
    }


    public function calculoEdad( $nacimiento){
         //calculo de la edad del actor:
         if( $nacimiento != NULL ){
            $hoy = date('Y-m-d');
            $edad = date_diff(date_create($nacimiento->format('Y-m-d')), date_create($hoy))->y;
            return $edad;
        }
        return "No hay fecha de nacimiento para poder calcular la edad";
    }
}
