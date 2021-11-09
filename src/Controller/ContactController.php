<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use Psr\Log\LoggerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact( Request $request, MailerInterface $mailer, LoggerInterface $appInfoLogger ){

        //creamos el formulario
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()){
            $datos = $form->getData();

            $email = new Email();
            $email->from($datos['email'])
                    ->to($this->getParameter('app.admin_email'))
                    ->subject($datos['asunto'])
                    ->text($datos['contenido']);

            $mailer->send($email);

            $mensaje = "Mail enviado correctamente ";
            $this->addFlash( 'success', $mensaje );
            $appInfoLogger->info( 'Email enviado de: '.$datos['email'] );

            return $this->redirectToRoute('portada');

        }

        return $this->renderForm('contact.html.twig', [
            "formulario" => $form
        ]);
    }
}