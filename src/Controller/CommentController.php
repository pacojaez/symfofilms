<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Comment;
use App\Entity\Movie;
use App\Form\CommentFormType;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'comment')]
    public function index(): Response {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    #[Route('/comment/create', name: 'comment_create', methods: ['POST'])]
    public function create( Request $request ): Response {

        $user = $this->getUser();
        $movie = $this->getDoctrine()->getRepository( Movie::class )->find($request->get('peli_id'));
        $content = $request->get('content');
        $comment = new Comment();

        // $commentFormulario = $this->createForm( CommentFormType::class, $comment );

        // $commentFormulario->handleRequest( $request );

        // if( $commentFormulario->isSubmitted() && $commentFormulario->isValid()){

            $comment->setUser( $user );
            $comment->addMovie( $movie );
            $comment->setContent($content);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist( $comment );
            $entityManager->flush();

            $mensaje = "Comentario añadido correctamente a la pelicula";
            $this->addFlash( 'success', $mensaje );

            return $this->redirectToRoute('movie_show', [
                'id' => $movie->getId(),
            ]);
        // }
        
        // return $this->render('comment/form.html.twig', [
            // 'commentFormulario' => $commentFormulario->createView(),
        // ]);
    }
}
