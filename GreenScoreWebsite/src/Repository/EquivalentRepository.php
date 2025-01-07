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

    // Recuperer un seul equivalent aleatoire
    public function findRandomEquivalents(int $count): array
    {
        // 1. Récupérer tous les IDs
        $qb = $this->createQueryBuilder('e')
            ->select('e.id');
        
        $ids = array_column($qb->getQuery()->getArrayResult(), 'id');
        
        if (empty($ids)) {
            return [];
        }
        
        // 2. Sélectionner aléatoirement le nombre d'IDs demandé
        shuffle($ids);
        $selectedIds = array_slice($ids, 0, min($count, count($ids)));
        
        // 3. Récupérer les entités correspondantes
        return $this->createQueryBuilder('e')
            ->where('e.id IN (:ids)')
            ->setParameter('ids', $selectedIds)
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
