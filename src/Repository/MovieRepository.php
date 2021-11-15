<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**  
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

     /**
     * @return Movie[] Returns an array of Movie objects
     */
    public function findByTitle ( string $value ): Array {   
       
        return $this->createQueryBuilder('m')
        ->where("m.titulo LIKE :value")
        ->setParameter('value', "%".$value."%")
        ->orderBy('m.id', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult()
        ;
    }
    // $pelis = $entityManager->createQuery( "SELECT m FROM App\Entity\Movie m WHERE m.valoracion > 3 ORDER BY m.valoracion DESC" ) ->getResult();
    

    /**
     * @return Movie[] Returns an array of Movie objects
     */
    public function findLastsWithCovers ( int $numeroPelis,  ): Array { 

        $em = $this->getEntityManager();

        $movies = $em->createQuery(
            'SELECT m
            FROM App\Entity\Movie m
            WHERE m.caratula IS NOT NULL
            ORDER BY m.id DESC
            '
        )
        ->setMaxResults( $numeroPelis )
        ->getResult();

        return $movies;
        
//         $pelisLastsWithCovers = $this->createQuery( "SELECT m FROM App\Entity\Movie m WHERE m.caratula IS NOT NULL ORDER BY m.id DESC LIMIT $numeroPelis " ) ->getResult();
// dd($pelisLastsWithCovers);
//         return $pelisLastsWithCovers;
    }

    /**
     * @return Movie[] Returns an array of Movie objects
     */
    public function findBetterValoration ( int $numeroPelis ): Array {

        $em = $this->getEntityManager();

        $pelisMejorValoradas= $em->createQuery( 
                " SELECT m 
                FROM App\Entity\Movie m 
                WHERE m.valoracion > 3 
                ORDER BY m.valoracion DESC "
            )
            ->setMaxResults( $numeroPelis )
             ->getResult();

        return $pelisMejorValoradas;
    }
   
   
}
