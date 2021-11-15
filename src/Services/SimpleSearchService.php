<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;


class SimpleSearchService {

    public $campo = 'id';
    public $valor = '%';
    public $orden = 'id';
    public $sentido = 'DESC';
    PUBLIC $limite = 5;

    private $entityManager;

    public function __construct( EntityManagerInterface $entityManager ){

        $this->entityManager = $entityManager;

    }

    public function search ( string $entityType ){
        
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

        return $consulta;
    }
}