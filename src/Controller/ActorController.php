<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Actor;

use App\Form\ActorFormType;
use App\Form\ActorDeleteFormType;
use App\Form\ImageDeleteFormType;
use App\Services\FileService;
use App\Services\PaginatorService;

class ActorController extends AbstractController
{
    #[Route('/all_actors/{pagina}', name: 'all_actors', defaults:['pagina'=>1], methods:'GET')]
    public function index( int $pagina, PaginatorService $paginator ): Response{

        $paginator->setEntityType('App\Entity\Actor');

        $actors = $paginator->findAllEntities( $pagina );

        // $actors = $this->getDoctrine()->getRepository( Actor::class )->findAll();

        return $this->render('actor/allactors.html.twig', [
            'actors' => $actors,
            'totalPaginas' => $paginator->getTotalPages(),
            'totalItems' => $paginator->getTotalItems(),
            'paginaActual' => $pagina,
            'entidad' => 'Actores'
        ]);
    }

    #[Route('/actor/create', name: 'actor_create')]
    public function create( Request $request, FileService $uploader ){
        
        $actor = new Actor();

        $formulario = $this->createForm( ActorFormType::class, $actor );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            $file = $formulario->get('portrait')->getData();

            // $file = $formulario->get('portrait')->getData();

            if( $file ){
                // $extension = $file->guessExtension();
                // $directorio = $this->getParameter('app.covers_root');
                // $fichero = uniqid().".$extension";
                // $file->move($directorio, $fichero);
                // $peli->setCaratula($fichero);
                $uploader->targetDirectory = $this->getParameter('app.portraits_root');
                $actor->setPortrait($uploader->upload($file));       // pasamos a tener una sola linea de código en vez de 5 tras implementar el servicio
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist( $actor );
            $entityManager->flush();

            // $nombre = $request->request->all();
            // $nombre = $request->query->all()['actor_form']['nombre'];
            $nombre = $request->get('actor_form')['nombre'];

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
    public function edit( Actor $actor, Request $request, FileService $uploader ){

        $fichero = $actor->getPortrait();

        $formulario = $this->createForm( ActorFormType::class, $actor );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

       //calculo de la edad del actor:
       $edad = $this->calculoEdad($actor->getFechaNacimiento());
        
        
        if($formulario->isSubmitted() && $formulario->isValid()){

            //calculo de la edad del actor:
            $edad = $this->calculoEdad($actor->getFechaNacimiento());

            // $file = $request->files->get('actor_form')['portrait'];
            $file = $formulario->get('portrait')->getData();
            
            if($file){
                // dd($file);
                // $directorio = $this->getParameter('app.covers_root');

                // if($fichero){
                //     $filesystem = new Filesystem();
                //     $filesystem->remove("$directorio/$fichero");
                // }

                // $extension = $file->guessExtension();
                // $fichero = uniqid()."$extension"; 
                // $file->move( $directorio, $fichero );
                $uploader->targetDirectory = $this->getParameter('app.portraits_root');
                $fichero = $uploader->replace( $file, $fichero );     
                $this->addFlash( 'success', 'Imagen actualizada correctamente' );
                // $this->addFlash( 'success', 'Imagenes reemplazadas correctamente' );
                // probado con exito el borrado del fichero: 618a94c1cef64.jpg
            }

            $actor->setPortrait($fichero);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $mensaje = "Actor ".$actor->getNombre()." con id: ".$actor->getId()." actualizado correctamente";
            $this->addFlash( 'success', $mensaje );
        
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
    public function delete( Actor $actor, Request $request, FileService $uploader ): Response {

        $formulario = $this->createForm( ActorDeleteFormType::class, $actor );

        //guardando la pelicula cuando llega el form
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){

            if( $actor->getPortrait() ){
                // $directorio = $this->getParameter('app.covers_root');
                // $filesystem = new Filesystem(); 
                // $filesystem->remove($directorio.'/'.$peli->getCaratula());
                $uploader->targetDirectory = $this->getParameter('app.portraits_root');

                if( $uploader->remove( $actor->getPortrait())){
                    $this->addFlash( 'success', 'Imagen borrada correctamente' );
                }else{
                    $this->addFlash( 'warning', 'Imagen no borrada' );
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actor);
            $entityManager->flush();

            $this->addFlash('success', 'Actor borrado correctamente');

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

    #[Route('/actor/deleteimage/{id}', name: 'actor_delete_image', methods:"GET")]
    public function deleteimage( Actor $actor, Request $request, FileService $uploader ): Response {
   
        if($actor->getPortrait()){

            // $filesystem = new Filesystem();
            // $directorio = $this->getParameter('app.covers_root');
            $uploader->targetDirectory = $this->getParameter('app.portraits_root');
            $uploader->remove( $actor->getPortrait() );

            if( $uploader->remove( $actor->getPortrait())){
                $this->addFlash( 'success', 'Imagen borrada correctamente' );
            }else{
                $this->addFlash( 'warning', 'Imagen no borrada' );
            }

            // $filesystem->remove($directorio.'/'.$peli->getCaratula());
            $actor->setPortrait(NULL);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $mensaje = "Imagen del actor ".$actor->getNombre()." borrada correctamente";
            $this->addFlash( 'success', $mensaje );
            
            return $this->redirectToRoute('actor_edit', ['id'=>$actor->getId()]);

        }

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
