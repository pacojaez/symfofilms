<?php


namespace App\Services;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManagerInterface;

class PaginatorService {

    /**
     * Propiedades
     */
     private int $limit; 
     private $entityManager; 
     private string $entityType = '';
     private int $paginaActual = 1;
     private int $total = 0;

    /**
     * CONSTRUCTOR PARA INICIALIZAR LA CLASE
     */

     public function __construct ( int $limit, EntityManagerInterface $entityManager ){

        $this->limit = $limit;
        $this->entityManager = $entityManager;

     }

    /**
     * SETTERS Y GETTERS NECESARIOS
     */

    public function setEntityType ( string $entityType ): void {
        $this->entityType = $entityType;
    }

    public function setLimit( int $limit ){
        $this->limit = $limit;
    }

    public function getPaginaActual(): int {
        return $this->paginaActual;
    }

    public function getTotalItems(): int {
        return $this->total;
    }

    public function getTotalPages(): int {
        return ceil($this->total / $this->limit);
    }

    /**
     * METODO PARA PAGINAR LOS RESULTADOS
     */
    public function paginate ( $dql, $page = 1 ): Paginator {
        $paginator = new Paginator( $dql );
        
        if( $this->limit * ( $page-1 ) >0){

            $paginator->getQuery()
            ->setFirstResult( $this->limit * ( $page-1 ))
            ->setMaxResults( $this->limit );
            $this->total = $paginator->count();

            return $paginator;
        }else{
            
            $paginator->getQuery()
            ->setFirstResult( $this->limit *( $page -1 ))
            ->setMaxResults( $this->limit )
            ->getResult();
            ;
            $this->paginaActual = $page;
            // dd($paginator->getQuery());
            $this->total = $paginator->count();

            return $paginator;
        }
       
    }

    /**
     * metodo para recuperar todas las entidades
     */
    public function findAllEntities ( int $paginaActual = 1 ): Paginator {

        $consulta = $this->entityManager->createQuery(
            "SELECT p
            FROM $this->entityType p
            ORDER BY p.id DESC "
        );
        return $this->paginate( $consulta, $paginaActual );
    }

    /**
     * metodo para recuperar todas las entidades con criterio de busqueda
     */
    public function findEntitiesSearchTerm ( int $paginaActual = 1, string $campo, string $valor ): Paginator {

        // $entityCampo = "p.".$campo;

        $consulta = $this->entityManager->createQuery(
            "SELECT p
            FROM $this->entityType p
            WHERE :campo LIKE :valor
            ORDER BY p.id D ESC "
        )
            ->setParameter("campo", $campo)
            ->setParameter("valor", '%'.$valor.'%')
            ->getResult()
        ;
        //  dd($consulta);
        return $this->paginate( $consulta, $paginaActual );
    }
}
