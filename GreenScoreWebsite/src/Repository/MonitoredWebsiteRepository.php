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
    public function getTop5PollutingSitesByUsers(array $usersIds): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.urlDomain', 'SUM(m.carbonFootprint) as totalFootprint')
            ->where('m.user IN (:usersIds)')
            ->setParameter('usersIds', $usersIds)
            ->groupBy('m.urlDomain')
            ->orderBy('totalFootprint', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function getConsuByFilter(array $usersIds, string $filter): array
    {
        $date = new \DateTime();
        $startDate = clone $date;

        switch ($filter) {
            case 'jour':
                $startDate->modify('-6 days');
                $select = 'DATE(m.creationDate) as period, SUM(m.carbonFootprint) as total_consumption';
                $groupBy = 'period';
                break;
            case 'semaine':
                $startDate->modify('-3 weeks')->modify('monday this week');
                $date->modify('sunday this week');
                // On récupère simplement la date de création pour le traitement ultérieur
                $select = 'DATE(m.creationDate) as period, SUM(m.carbonFootprint) as total_consumption';
                $groupBy = 'period';
                break;
            case 'mois':
                $startDate->modify('-11 months');
                $select = 'MONTH(m.creationDate) as month, YEAR(m.creationDate) as year, SUM(m.carbonFootprint) as total_consumption';
                $groupBy = 'month, year';
                break;
            default:
                throw new \InvalidArgumentException('Filtre non valide');
        }

        $qb = $this->createQueryBuilder('m')
            ->select($select)
            ->where('m.user IN (:usersIds)')
            ->andWhere('m.creationDate BETWEEN :startDate AND :endDate')
            ->setParameter('usersIds', $usersIds)
            ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
            ->setParameter('endDate', $date->format('Y-m-d H:i:s'));

        if ($groupBy) {
            $qb->groupBy($groupBy);
        }

        return $qb->getQuery()->getResult();
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
