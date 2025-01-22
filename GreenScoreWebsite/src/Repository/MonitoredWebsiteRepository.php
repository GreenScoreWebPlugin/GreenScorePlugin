<?php

namespace App\Repository;

use App\Entity\MonitoredWebsite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MonitoredWebsite>
 */
class MonitoredWebsiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MonitoredWebsite::class);
    }
    
    // Recupere la derniere entree ajoutee en fonction de userId
    public function findLastAddedByUser(int $userId): ?MonitoredWebsite
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('m.id', 'DESC') 
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Recupere la moyenne de l'empreinte carbone sur une journée en fonction de userId
    public function getAverageDailyCarbonFootprint(int $userId): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('SUBSTRING(m.creationDate, 1, 10) as day, AVG(m.carbonFootprint) as dailyAverage')
            ->where('m.user = :userId')
            ->setParameter('userId', $userId)
            ->groupBy('day');

        $dailyAverages = $qb->getQuery()->getResult();
        
        if (empty($dailyAverages)) {
            return 0.0;
        }
        
        $sum = 0;
        foreach ($dailyAverages as $daily) {
            $sum += $daily['dailyAverage'];
        }
        
        return round($sum / count($dailyAverages), 2);
    }

    // Recupere la moyenne de l'empreinte carbone sur une journée sur l'ensemble des utilisateurs
    public function getGlobalAverageDailyCarbonFootprint(): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('SUBSTRING(m.creationDate, 1, 10) as day, IDENTITY(m.user) as userId, AVG(m.carbonFootprint) as dailyAverage')
            ->groupBy('day', 'm.user');

        $dailyAverages = $qb->getQuery()->getResult();
        
        if (empty($dailyAverages)) {
            return 0.0;
        }
        
        $sum = 0;
        foreach ($dailyAverages as $daily) {
            $sum += $daily['dailyAverage'];
        }
        
        return round($sum / count($dailyAverages), 2);
    }

    // Recupere le top5 des sites les plus polluants en fonction d'une liste d'utilisateurs
    public function getTop5PollutingSitesByUsers(array $userIds): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.urlDomain', 'SUM(m.carbonFootprint) as totalFootprint')
            ->where('m.user IN (:userIds)')
            ->setParameter('userIds', $userIds)
            ->groupBy('m.urlDomain')
            ->orderBy('totalFootprint', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return MonitoredWebsite[] Returns an array of MonitoredWebsite objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MonitoredWebsite
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
