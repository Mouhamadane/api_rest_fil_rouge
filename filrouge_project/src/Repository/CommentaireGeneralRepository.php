<?php

namespace App\Repository;

use App\Entity\CommentaireGeneral;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentaireGeneral|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentaireGeneral|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentaireGeneral[]    findAll()
 * @method CommentaireGeneral[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireGeneralRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentaireGeneral::class);
    }

    public function findChatByApprenanAndPromo($idp,$ida): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT c
        FROM App\Entity\CommentaireGeneral c
        JOIN c.filDeDiscussion fd
        JOIN  fd.promo p
        JOIN p.user u
        WHERE p.id = :idp
        AND u.id =:ida'
        )->setParameter('idp', $idp)
        ->setParameter('ida', $ida)
        ;

        return $query->getResult();

    }

    // /**
    //  * @return CommentaireGeneral[] Returns an array of CommentaireGeneral objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentaireGeneral
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
