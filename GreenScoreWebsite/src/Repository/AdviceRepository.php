<?php

namespace App\Repository;

use App\Entity\Advice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Advice>
 */
class AdviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advice::class);
    }

    // Récupère un conseil aléatoire en fonction de la valeur de isDev
    public function findRandomByIsDev(bool $isDev): ?Advice
    {
        // Crée une requête pour récupérer les IDs des conseils correspondant à isDev
        $qb = $this->createQueryBuilder('a')
            ->select('a.id')
            ->where('a.isDev = :isDev') // Filtre par isDev
            ->setParameter('isDev', $isDev);

        // Exécute la requête et extrait les IDs sous forme de tableau
        $ids = array_column($qb->getQuery()->getArrayResult(), 'id');

        // Si aucun conseil n'est trouvé, retourne null
        if (empty($ids)) {
            return null;
        }

        // Sélectionne un ID aléatoire parmi les résultats
        $randomId = $ids[array_rand($ids)];

        // Retourne le conseil correspondant à l'ID aléatoire
        return $this->find($randomId);
    }


    //    /**
    //     * @return Advice[] Returns an array of Advice objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Advice
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
