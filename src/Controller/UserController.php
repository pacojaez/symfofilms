<?php

namespace App\Controller;

use DoctrineExtensions\Query\Mysql\Binary;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

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
}
