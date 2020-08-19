<?php

namespace App\Repository;

use App\Entity\LivrablesAttendus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LivrablesAttendus|null find($id, $lockMode = null, $lockVersion = null)
 * @method LivrablesAttendus|null findOneBy(array $criteria, array $orderBy = null)
 * @method LivrablesAttendus[]    findAll()
 * @method LivrablesAttendus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivrablesAttendusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LivrablesAttendus::class);
    }

    // /**
    //  * @return LivrablesAttendus[] Returns an array of LivrablesAttendus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LivrablesAttendus
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
