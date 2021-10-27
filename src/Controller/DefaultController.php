<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Movie;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'portada')]
    public function index(): Response
    {   
        $peliculas = $this->getDoctrine()->getRepository( Movie::class )->findAll();
        return $this->render('portada.html.twig', [
            'controller_name' => 'DefaultController',
            'peliculas' => $peliculas,
        ]);
    }
    

    #[Route('/docs', name: 'docs')]
    public function docs(){
        return $this->render('docs.html.twig');
    }

    #[Route('/components', name: 'components')]
    public function components(){
        return $this->render('components.html.twig');
    }
}
