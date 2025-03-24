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
    
    // Récupère la dernière entrée ajoutée pour un utilisateur donné
    public function findLastAddedByUser(int $userId): ?MonitoredWebsite
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.user = :userId') // Filtre par userId
            ->setParameter('userId', $userId)
            ->orderBy('m.id', 'DESC')  // Trie par ID décroissant pour obtenir le dernier
            ->setMaxResults(1)  // Limite à un seul résultat
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Récupère la moyenne de l'empreinte carbone journalière pour une liste d'utilisateurs
    public function getAverageDailyCarbonFootprint(array $usersIds): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('SUBSTRING(m.creationDate, 1, 10) as day, AVG(m.carbonFootprint) as dailyAverage')
            ->where('m.user IN (:usersIds)') // Filtre par liste d'utilisateurs
            ->setParameter('usersIds', $usersIds)
            ->groupBy('day');  // Groupement par jour

        $dailyAverages = $qb->getQuery()->getResult();
        
        if (empty($dailyAverages)) {
            return 0.0;  // Retourne 0 si aucune donnée n'est disponible
        }

        $sum = 0;
        foreach ($dailyAverages as $daily) {
            $sum += $daily['dailyAverage'];  // Somme des moyennes journalières
        }
        
        return round($sum / count($dailyAverages), 2);  // Moyenne des moyennes, arrondie à 2 décimales
    }

    // Récupère la moyenne de l'empreinte carbone journalière globale
    public function getGlobalAverageDailyCarbonFootprint(): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('SUBSTRING(m.creationDate, 1, 10) as day, IDENTITY(m.user) as userId, AVG(m.carbonFootprint) as dailyAverage')
            ->groupBy('day', 'm.user');  // Groupement par jour et utilisateur

        $dailyAverages = $qb->getQuery()->getResult();
        
        if (empty($dailyAverages)) {
            return 0.0;  // Retourne 0 si aucune donnée n'est disponible
        }

        $sum = 0;
        foreach ($dailyAverages as $daily) {
            $sum += $daily['dailyAverage'];  // Somme des moyennes journalières
        }
        
        return round($sum / count($dailyAverages), 2);  // Moyenne globale, arrondie à 2 décimales
    }

    // Récupère les 5 sites les plus polluants pour un ensemble d'utilisateurs
    public function getTop5PollutingSitesByUsers(array $usersIds): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.urlDomain', 'SUM(m.carbonFootprint) as totalFootprint')
            ->where('m.user IN (:usersIds)')  // Filtre par utilisateurs
            ->setParameter('usersIds', $usersIds)
            ->groupBy('m.urlDomain')  // Groupement par domaine de site
            ->orderBy('totalFootprint', 'DESC')  // Trie par empreinte carbone totale décroissante
            ->setMaxResults(5)  // Limite à 5 résultats
            ->getQuery()
            ->getResult();
    }

    // Récupère la consommation carbone filtrée par jour, semaine ou mois
    public function getConsuByFilter(array $usersIds, string $filter): array
    {
        $date = new \DateTime();
        $startDate = clone $date;

        switch ($filter) {
            case 'jour':
                $startDate->modify('-6 days');  // 7 derniers jours
                $select = 'DATE(m.creationDate) as period, SUM(m.carbonFootprint) as total_consumption';
                $groupBy = 'period';
                break;
            case 'semaine':
                $startDate->modify('-3 weeks')->modify('monday this week');  // Semaine précédente
                $date->modify('sunday this week');
                $select = 'DATE(m.creationDate) as period, SUM(m.carbonFootprint) as total_consumption';
                $groupBy = 'period';
                break;
            case 'mois':
                $startDate->modify('-11 months');  // 12 derniers mois
                $select = 'MONTH(m.creationDate) as month, YEAR(m.creationDate) as year, SUM(m.carbonFootprint) as total_consumption';
                $groupBy = 'month, year';
                break;
            default:
                throw new \InvalidArgumentException('Filtre non valide');  // Si le filtre n'est pas valide
        }

        $qb = $this->createQueryBuilder('m')
            ->select($select)
            ->where('m.user IN (:usersIds)')
            ->andWhere('m.creationDate BETWEEN :startDate AND :endDate')
            ->setParameter('usersIds', $usersIds)
            ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
            ->setParameter('endDate', $date->format('Y-m-d H:i:s'));

        if ($groupBy) {
            $qb->groupBy($groupBy);  // Groupement en fonction du filtre
        }

        return $qb->getQuery()->getResult();  // Retourne les résultats filtrés
    }

    // Récupère la consommation totale de carbone pour un ensemble d'utilisateurs
    public function getTotalConsuOrga(array $usersIds): float
    {
        return $this->createQueryBuilder('m')
            ->select('SUM(m.carbonFootprint) as totalFootprint')
            ->where('m.user IN (:usersIds)')  // Filtre par utilisateurs
            ->setParameter('usersIds', $usersIds)
            ->getQuery()
            ->getSingleScalarResult();  // Retourne la somme totale
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
