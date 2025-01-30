<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    // Récupère la moyenne de l'empreinte carbone sur l'ensemble des utilisateurs
    public function getGlobalAverageCarbonFootprint(): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select(' AVG(m.totalCarbonFootprint) as averageConsumption')
            ->where('m.totalCarbonFootprint IS NOT NULL')
            ->andWhere('m.totalCarbonFootprint > 0');

        $averageConsumption = $qb->getQuery()->getResult();

        if (empty($averageConsumption)) {
            return 0.0;
        }

        $sum = 0;
        foreach ($averageConsumption as $average) {
            $sum += $average['averageConsumption'];
        }

        return round($sum / count($averageConsumption), 2);
    }

    // Récupère la moyenne de l'empreinte carbone sur l'ensemble des entreprise
    public function getOrganisationGlobalAverageCarbonFootprint(): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select(' AVG(m.totalCarbonFootprint) as averageConsumption, IDENTITY(m.organisation) as organisationId')
            ->where('m.totalCarbonFootprint IS NOT NULL')
            ->andWhere('m.totalCarbonFootprint > 0')
            ->groupBy('organisationId');

        $averageConsumption = $qb->getQuery()->getResult();

        if (empty($averageConsumption)) {
            return 0.0;
        }

        $sum = 0;
        foreach ($averageConsumption as $average) {
            $sum += $average['averageConsumption'];
        }

        return round($sum / count($averageConsumption), 2);
    }

    // Récupère l'utilisateur ayant le moins consommé
    public function getLeastConsumptionCarbonFootprint(): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('MIN(m.totalCarbonFootprint) as leastConsumption')
            ->where('m.totalCarbonFootprint IS NOT NULL')
            ->andWhere('m.totalCarbonFootprint > 0'); // Ignore les valeurs NULL et 0

        $result = $qb->getQuery()->getSingleResult();

        return round($result['leastConsumption'], 2);
    }

    public function getOrganisationLeastConsumptionCarbonFootprint(): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('SUM(m.totalCarbonFootprint) as totalConsumption, IDENTITY(m.organisation) as organisationId')
            ->where('m.totalCarbonFootprint IS NOT NULL')
            ->andWhere('m.totalCarbonFootprint > 0')
            ->groupBy('organisationId')
            ->orderBy('totalConsumption', 'ASC') // Trie pour avoir le plus petit en premier
            ->setMaxResults(1); // Récupère seulement l'organisation avec la plus petite consommation totale

        $result = $qb->getQuery()->getOneOrNullResult(); // Permet d'éviter une erreur si aucun résultat

        if (!$result) {
            return 0.0;
        }

        return round($result['totalConsumption'], 2);
    }



    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
