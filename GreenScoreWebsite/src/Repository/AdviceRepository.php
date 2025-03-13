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

    // Recupere un conseil aleatoire en fonction de isDev
    public function findRandomByIsDev(bool $isDev): ?Advice
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id')
            ->where('a.isDev = :isDev')
            ->setParameter('isDev', $isDev);

        $ids = array_column($qb->getQuery()->getArrayResult(), 'id');

        if (empty($ids)) {
            return null;
        }

        $randomId = $ids[array_rand($ids)];

        return $this->find($randomId);
    }

    public function getAllAdvice(): ?Advice
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a');

        
        $advices = $qb->getQuery()->getResult();

        if (empty($advices)) {
            return null;
        }

        return $advices;
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
