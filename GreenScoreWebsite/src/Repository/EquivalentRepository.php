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

    // Récupère un certain nombre d'équivalents aléatoires en fonction du ratio
    public function findRandomEquivalents(int $count, float $ratio): array
    {
        // 1. Récupère les IDs des équivalents dont la valeur ajustée par le ratio est dans un certain intervalle
        $qb = $this->createQueryBuilder('e')
            ->select('e.id, e.equivalent')
            ->andWhere('e.equivalent * :ratio > 0.1') // Limite la valeur minimale après ajustement
            ->andWhere('e.equivalent * :ratio < 500') // Limite la valeur maximale après ajustement
            ->setParameter('ratio', $ratio);

        // Exécute la requête et extrait les IDs des résultats
        $ids = array_column($qb->getQuery()->getArrayResult(), 'id');

        // Si aucun ID n'est trouvé, retourne un tableau vide
        if (empty($ids)) {
            return [];
        }

        // 2. Sélectionne aléatoirement les IDs demandés
        shuffle($ids);
        $selectedIds = array_slice($ids, 0, min($count, count($ids)));

        // 3. Récupère les entités correspondant aux IDs sélectionnés
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
