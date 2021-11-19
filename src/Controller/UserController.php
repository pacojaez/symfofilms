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
use App\Services\FileService;
use Psr\Log\LoggerInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

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

    #[Route('/use/update/{id}', name: 'user_update', methods: ['GET', 'POST'])]
    public function update( Request $request, LoggerInterface $appUserLogger, FileService $uploader ): Response {

        $user = $this->getUser();
        
        $formulario = $this->createForm( UserUpdateFormType::class, $user );
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            $uploader->targetDirectory = $this->getParameter('app.users_pics_root');

            if( $user->getAvatar())
                $uploader->delete($user->getAvatar());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $this->container->get('security.token_storage')->setToken(NULL);
            $this->container->get('session')->invalidate();

            $mensaje = "Usuario ".$user->getDisplayname()." borrado correctamente";
            $this->addFlash('success', $mensaje);

            $mensaje = "El Usuario ".$user->getDisplayname()." se ha dado de baja";
            $appUserLogger->warning($mensaje);

            return $this->redirectToRoute('portada');
        }
        
        return $this->renderForm('user/update.html.twig', [
            'formulario' => $formulario,
            'user' => $user
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

        //     return $this->redirectToRoute('movie_edit', ['id'=>$peli->getId()]);

        // }

    }
}
