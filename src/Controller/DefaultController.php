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
        return $this->render('admin/docs.html.twig');
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
        return $this->render('admin/todo.html.twig');
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

                //TODO contar el numero de busquedas de cada término y añadirlo a la vista
            }

            return $this->render('admin/searchlog.html.twig', [
                'resultado' => $resultado,
            ]);
        }else{
            return $this->render('admin/searchlog.html.twig', [
                'resultado' => [],
            ]);

        }
    
    }

    /**
     * @Route("/usersactions", name="usersactions")
     */
    public function userslogs(): Response {

        // [2021-11-18T10:40:58.445222+01:00] app_user.INFO: Usuario nuevo registrado. Pendiente de verificar. Email: pacojaez@gmail.com [] [] //linea que es guardada en el log de search
        if( file_exists('..\var\log\appusers.log')){           //evita error de que no exista el archivo

            $logs = file('..\var\log\appusers.log');
            $resultado = [];

            foreach( $logs as $log ){
                // dd($log);
                $verified = '';
                $logLimpio = str_replace('[]', ' ', $log);
                $userAction = substr( $logLimpio, 1, 19);
                $userAction .= ': '.substr( $logLimpio, 50, (strlen($logLimpio)) );
                if ( strstr( $log, 'INFO' ) ) {
                    $status = 'Verificado';
                  } elseif  (strstr( $log, 'NOTICE' )){
                    $status = 'Registrado';
                  } elseif (strstr( $log, 'WARNING' )){
                    $status = 'Baja';
                  }else{
                    $status = '';
                  }
                array_push( $resultado, ['action' => $userAction, 'status'=>$status] );

                //TODO poner fondo distinto si es registro o verificación o baja
            }

            return $this->render('admin/usersAction.html.twig', [
                'resultado' => $resultado,
            ]);
        }else{
            return $this->render('admin/usersAction.html.twig', [
                'resultado' => [],
            ]);

        }
    
    }
}
