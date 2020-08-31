<?php

namespace App\Repository;

use App\Entity\BriefLA;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BriefLA|null find($id, $lockMode = null, $lockVersion = null)
 * @method BriefLA|null findOneBy(array $criteria, array $orderBy = null)
 * @method BriefLA[]    findAll()
 * @method BriefLA[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BriefLARepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BriefLA::class);
    }

    // /**
    //  * @return BriefLA[] Returns an array of BriefLA objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BriefLA
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
