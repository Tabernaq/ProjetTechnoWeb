<?php

namespace App\Repository;

use App\Entity\Goat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Goat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Goat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Goat[]    findAll()
 * @method Goat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Goat::class);
    }

    // /**
    //  * @return Goat[] Returns an array of Goat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Goat
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
