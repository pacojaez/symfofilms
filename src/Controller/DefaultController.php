<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Movie;

#[Route("/admin", name:"admin_")]
class DefaultController extends AbstractController
{
      
    #[Route('/docs', name: 'docs')]
    public function docs(){
        return $this->render('docs.html.twig');
    }

    #[Route('/', name: 'admin')]
    public function admin(){
        return $this->render('admin.html.twig');
    }

    #[Route('/components', name: 'components')]
    public function components(){
        return $this->render('components.html.twig');
    }
    #[Route('/todo', name: 'todo')]
    public function todo(){
        return $this->render('todo.html.twig');
    }

    /**
     * @Route("/searchlogs", name="searchlogs")
     */
    public function searchlogs(): Response {

        // "[2021-11-03T15:22:50.601744+00:00] app_search.INFO: Se ha buscado el término: Peter [] []" //linea que es guardada en el log de search
        if( file_exists('..\var\log\appsearch.log')){           //evita error de que no exista el archivo

            $logs = file('..\var\log\appsearch.log');
            $resultado = [];

            foreach( $logs as $log ){
                $logLimpio = str_replace('[]', ' ', $log);
                $terminoBusqueda = substr( $logLimpio, 1, 10);
                $terminoBusqueda .= ': '.substr( $logLimpio, 79, (strlen($logLimpio)) );
                array_push( $resultado, $terminoBusqueda );

                //TODO contar el numero de busquedas de caada término y añadirlo a la vista
            }

            return $this->render('movie/searchlog.html.twig', [
                'resultado' => $resultado,
            ]);
        }else{
            return $this->render('movie/searchlog.html.twig', [
                'resultado' => [],
            ]);

        }
        

    }
}
