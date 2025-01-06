<?php

namespace App\Repository;

use App\Entity\Equivalent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equivalent>
 */
class EquivalentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equivalent::class);
    }

    // Recupere deux equivalents aleatoires
    public function findTwoRandomEquivalents(): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.id');

        $ids = array_column($qb->getQuery()->getArrayResult(), 'id');

        if (empty($ids)) {
            return [];
        }

        $randomIds = array_rand($ids, min(2, count($ids)));

        if (!is_array($randomIds)) {
            $randomIds = [$randomIds];
        }

        return $this->createQueryBuilder('e')
            ->where('e.id IN (:ids)')
            ->setParameter('ids', array_intersect_key($ids, array_flip($randomIds)))
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Equivalent[] Returns an array of Equivalent objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Equivalent
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
