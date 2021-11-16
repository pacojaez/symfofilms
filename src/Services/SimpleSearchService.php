<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class SimpleSearchService {

    public $campo = 'id';
    public $valor = '';
    public $orden = 'id';
    public $sentido = 'DESC';
    public $limite = 5;

    private $entityManager;
    private LoggerInterface $appSearchLogger;

    public function __construct( EntityManagerInterface $entityManager,  LoggerInterface $appSearchLogger ){

        $this->entityManager = $entityManager;
        $this->appSearchLogger = $appSearchLogger;

    }

    public function search (string $entityType): array {
        
        $consulta = $this->entityManager->createQuery(
            "SELECT p 
            FROM $entityType p
            WHERE p.$this->campo LIKE :valor
            ORDER BY p.$this->orden $this->sentido
            "
        )
        ->setParameter('valor', '%'.$this->valor.'%')
        ->setMaxResults($this->limite)
        ->getResult();

        // guardando los términos de busqueda en el log
        if( $this->valor != '' || $this->valor != NULL )
            $this->appSearchLogger->info( "Se ha buscado el término: ".$this->valor );

        return $consulta;
    }
}