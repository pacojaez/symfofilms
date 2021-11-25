<?php

namespace App\Controller;

use DoctrineExtensions\Query\Mysql\Binary;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserUpdateFormType;
use Symfony\Component\Filesystem\Filesystem;
use App\Services\PaginatorService;
use App\Services\SimpleSearchService;
use App\Services\FileService;
use Psr\Log\LoggerInterface;

USE App\Entity\User;
use App\Form\RoleFormType;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    // #[Route('/user', name: 'user')]
    // public function index(): Response
    // {
    //     return $this->render('user/index.html.twig', [
    //         'controller_name' => 'UserController',
    //     ]);
    // }

    #[Route('/user/pic/{avatar}', name: 'pic_show', methods: ['GET']) ]
    public function showPic( string $avatar ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $ruta = $this->getParameter('app.users_pics_root');

        $response = new BinaryFileResponse( $ruta.'/'.$avatar );
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $avatar,
            iconv('UTF-8', 'ASCII//TRANSLIT', $avatar )
        );
        
        return $response;
    }

    #[Route('/user/edit/{id}', name: 'user_edit', methods: ['GET', 'POST'])]
    public function update( Request $request, LoggerInterface $appUserLogger, FileService $uploader ): Response {

        $user = $this->getUser();

        $this->denyAccessUnlessGranted('edit', $user );
        // dd( $request->get('id'));
        // if( $user->getId() != $request->get('id') ){

        //     $this->addFlash('warning', 'No tienes permiso para realizar la operación');
        //     return $this->redirectToRoute('portada');
        // }
        
        $role_form = $this->createForm( RoleFormType::class );

        $fichero = $user->getAvatar();
        // dd($fichero);
        
        $formulario = $this->createForm( UserUpdateFormType::class, $user );
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            $file = $formulario->get('avatar')->getData();

            if($file){

                $uploader->targetDirectory = $this->getParameter('app.users_pics_root');

                $fichero = $uploader->replace( $file, $fichero);

                $this->addFlash('success', 'Avatar correctamente cambiado');

            }

            $user->setAvatar($fichero);

            $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->remove($user);
            $entityManager->flush();

            // $this->container->get('security.token_storage')->setToken(NULL);
            // $this->container->get('session')->invalidate();

            $mensaje = "Usuario ".$user->getDisplayname()." actualizado correctamente";
            $this->addFlash('success', $mensaje);

            // $mensaje = "El Usuario ".$user->getDisplayname()." se ha dado de baja";
            // $appUserLogger->warning($mensaje);

            return $this->redirectToRoute('home');
        }
        
        return $this->renderForm('user/edit.html.twig', [
            'formulario' => $formulario,
            'user' => $user,
            'role_form' => $role_form
        ]);
    }

    #[Route('/user/deleteimage/{id}', name: 'user_delete_image', methods:["GET"])]
    public function deleteimage( User $user, Request $request, LoggerInterface $appInfoLogger, FileService $uploader ): Response {
   
        // if($peli->getCaratula()){

        //     // $filesystem = new Filesystem();
        //     // $directorio = $this->getParameter('app.covers_root');
        //     $uploader->targetDirectory = $this->getParameter('app.covers_root');
        //     $uploader->remove( $peli->getCaratula() );

        //     if( $uploader->remove( $peli->getCaratula())){
        //         $this->addFlash( 'success', 'Imagen borrada correctamente' );
        //     }else{
        //         $this->addFlash( 'warning', 'Imagen no borrada' );
        //     }

        //     // $filesystem->remove($directorio.'/'.$peli->getCaratula());
        //     $peli->setCaratula(NULL);
        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager->flush();

        //     $mensaje = "Carátula de la Pelicula ".$peli->getTitulo()." borrada correctamente";
        //     $this->addFlash( 'success', $mensaje );
        //     $appInfoLogger->info( $mensaje );

        return $this->redirectToRoute( 'portada' );

        // }

    }

    #[Route('/allusers/{pagina}', name: 'all_users', defaults: ['pagina'=> 1], methods: ['GET'] )]
    public function allusers( int $pagina,  PaginatorService $paginator ): Response {

        $user = $this->getUser();
        $this->denyAccessUnlessGranted('show', $user);

        $paginator->setEntityType('App\Entity\User');

        $users = $paginator->findAllEntities( $pagina );

        return $this->render('user/allusers.html.twig', [
            'users' => $users,
            'totalPaginas' => $paginator->getTotalPages(),
            'totalItems' => $paginator->getTotalItems(),
            'paginaActual' => $pagina,
            'entidad' => 'Usuarios'
        ]);
    }

    #[Route('/users/search/{pagina}', name: 'users_search', methods: ['POST'], defaults: ['pagina'=> 1 ] )]
    #@
    public function search ( int $pagina, Request $request, SimpleSearchService $busqueda, PaginatorService $paginator ): Response {

        $user = $this->getUser();
        $this->denyAccessUnlessGranted('show', $user);

        $paginator->setEntityType('App\Entity\User');
        
        $valor = $request->get('valor');
        $busqueda->valor = $valor;
        
        $orden = 'email';
        $busqueda->orden = $orden;
    
        $campo = 'email';
        $busqueda->campo = $campo;

        $entityType = 'App\Entity\User';
       
        $users = $busqueda->search( 'App\Entity\User');

        // $users = $paginator->findEntitiesSearchTerm( $pagina, $campo, $valor );

        return $this->render('user/userssearch.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/show/{id}", name="user_show")
     * 
     * metodo show hecho de forma "tradicional"
     */
    public function show( User $user ): Response {

        $user = $this->getDoctrine()->getRepository( User::class )->find($user);

        $this->denyAccessUnlessGranted('show', $user);

        if(!$user)
            throw $this->createNotFoundException( "No se encontró el usuario con id: $user." );

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]); 
    }

    #[Route('/user/delete/{id}', name:'user_delete')]
    public function delete( User $user, Request $request, LoggerInterface $appInfoLogger, FileService $uploader ): Response {

        $user = new User();
        $this->denyAccessUnlessGranted('delete', $user);

        // $formulario = $this->createForm( MovieDeleteFormType::class, $peli );

        // //guardando la pelicula cuando llega el form
        // $formulario->handleRequest($request);

        // if($formulario->isSubmitted() && $formulario->isValid()){

        //     if( $peli->getCaratula() ){
        //         // $directorio = $this->getParameter('app.covers_root');
        //         // $filesystem = new Filesystem(); 
        //         // $filesystem->remove($directorio.'/'.$peli->getCaratula());
        //         $uploader->targetDirectory = $this->getParameter('app.covers_root');

        //         if( $uploader->remove( $peli->getCaratula())){
        //             $this->addFlash( 'success', 'Imagen borrada correctamente' );
        //         }else{
        //             $this->addFlash( 'warning', 'Imagen no borrada' );
        //         }
        //     }

        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager->remove($peli);
        //     $entityManager->flush();

        //     $mensaje = "Pelicula ".$peli->getTitulo()." borrada correctamente";
        //     $this->addFlash( 'success', $mensaje );
        //     $appInfoLogger->info( $mensaje );

        //     return $this->redirectToRoute('all_movies');
        // }

        // return $this->render('movie/delete.html.twig', [
        //     'formulario'=>$formulario->createView(),
        //     'peli' => $peli
        // ]);
        return new Response();
    }

    /**
     * @Route("/user/addRole/{id}", name="add_role", methods="POST" )
     */
    public function addRole( User $user, Request $request, EntityManagerInterface $em ): Response {
        dd($request);
        $newRole = $request->get('role');

        if( $user ){
            $roles = $user->getRoles();
            $roles [] = $newRole;
            $user->setRoles($roles);
            $em->flush( );

            $msg = 'Role añadido correctamente al usuario '.$user->getDisplayname();
            $this->addFlash('success', $msg);

            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
        }

        $msg = 'No se pudo añadir el Role al usuario '.$user->getDisplayname();
        $this->addFlash('warning', $msg);

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]); 
    }

}
