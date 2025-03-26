<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/*!
 * Cette classe est le repository qui permet de trouver les utilisateurs.
 */
/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // Récupère une liste paginée des utilisateurs pour une organisation donnée
    public function paginateUsers(int $page, int $limit, int $organisationId): Paginator
    {
        return new Paginator($this
            ->createQueryBuilder('u')
            ->andWhere('u.organisation = :organisation') // Filtre par organisation
            ->setParameter('organisation', $organisationId)
            ->setFirstResult(($page - 1) * $limit)  // Calcul de l'offset pour la pagination
            ->setMaxResults($limit)  // Limite du nombre de résultats par page
            ->getQuery()
            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),  // Désactive DISTINCT pour optimiser la requête
            false
        );
    }

    // Recherche d'utilisateurs avec une pagination et un terme de recherche optionnel
    public function searchUsers(int $page, int $limit, int $organisationId, ?string $searchTerm = null): Paginator
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.organisation = :organisation') // Filtre par organisation
            ->setParameter('organisation', $organisationId)
            ->orderBy('u.lastName', 'ASC');  // Trie par nom de famille

        if ($searchTerm && trim($searchTerm) !== '') {  // Applique le filtre de recherche si un terme est spécifié
            $searchTerm = trim($searchTerm);
            $qb->andWhere('
                LOWER(u.firstName) LIKE LOWER(:searchTerm) OR 
                LOWER(u.lastName) LIKE LOWER(:searchTerm) OR 
                LOWER(u.email) LIKE LOWER(:searchTerm)
            ')
            ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        // Calcul de l'offset pour la pagination
        $firstResult = ($page - 1) * $limit;

        $query = $qb->setFirstResult($firstResult)
            ->setMaxResults($limit)  // Limite le nombre de résultats par page
            ->getQuery()
            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false);

        return new Paginator($query, false);  // Retourne la liste paginée des utilisateurs
    }

    /**
     * Met à jour (rehache) le mot de passe de l'utilisateur automatiquement
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // Vérifie que l'objet $user est bien une instance de User
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);  // Met à jour le mot de passe
        $this->getEntityManager()->persist($user);  // Persiste l'utilisateur mis à jour
        $this->getEntityManager()->flush();  // Enregistre les modifications dans la base de données
    }

    // Récupère la moyenne de l'empreinte carbone de tous les utilisateurs
    public function getGlobalAverageCarbonFootprint(): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('AVG(m.totalCarbonFootprint) as averageConsumption')
            ->where('m.totalCarbonFootprint IS NOT NULL')
            ->andWhere('m.totalCarbonFootprint > 0');  // Ignore les valeurs NULL et 0

        $averageConsumption = $qb->getQuery()->getResult();

        if (empty($averageConsumption)) {
            return 0.0;  // Retourne 0 si aucune donnée n'est disponible
        }

        $sum = 0;
        foreach ($averageConsumption as $average) {
            $sum += $average['averageConsumption'];  // Somme des moyennes
        }

        return round($sum / count($averageConsumption), 2);  // Retourne la moyenne générale
    }

    // Récupère la moyenne de l'empreinte carbone des utilisateurs d'une organisation
    public function getOrganisationGlobalAverageCarbonFootprint(): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('AVG(m.totalCarbonFootprint) as averageConsumption, IDENTITY(m.organisation) as organisationId')
            ->where('m.totalCarbonFootprint IS NOT NULL')
            ->andWhere('m.totalCarbonFootprint > 0')
            ->groupBy('organisationId');  // Groupement par organisation

        $averageConsumption = $qb->getQuery()->getResult();

        if (empty($averageConsumption)) {
            return 0.0;  // Retourne 0 si aucune donnée n'est disponible
        }

        $sum = 0;
        foreach ($averageConsumption as $average) {
            $sum += $average['averageConsumption'];  // Somme des moyennes des organisations
        }

        return round($sum / count($averageConsumption), 2);  // Retourne la moyenne des organisations
    }

    // Récupère l'utilisateur ayant l'empreinte carbone la plus faible
    public function getLeastConsumptionCarbonFootprint(): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('MIN(m.totalCarbonFootprint) as leastConsumption')
            ->where('m.totalCarbonFootprint IS NOT NULL')
            ->andWhere('m.totalCarbonFootprint > 0');  // Ignore les valeurs NULL et 0

        $result = $qb->getQuery()->getSingleResult();

        return round($result['leastConsumption'], 2);  // Retourne la consommation la plus faible
    }

    // Récupère l'organisation avec l'empreinte carbone la plus faible
    public function getOrganisationLeastConsumptionCarbonFootprint(): float
    {
        $qb = $this->createQueryBuilder('m')
            ->select('SUM(m.totalCarbonFootprint) as totalConsumption, IDENTITY(m.organisation) as organisationId')
            ->where('m.totalCarbonFootprint IS NOT NULL')
            ->andWhere('m.totalCarbonFootprint > 0')
            ->groupBy('organisationId')  // Groupement par organisation
            ->orderBy('totalConsumption', 'ASC')  // Trie par consommation totale croissante
            ->setMaxResults(1);  // Limite à l'organisation avec la consommation la plus faible

        $result = $qb->getQuery()->getOneOrNullResult();  // Récupère le résultat ou null

        if (!$result) {
            return 0.0;  // Retourne 0 si aucune organisation n'est trouvée
        }

        return round($result['totalConsumption'], 2);  // Retourne la consommation totale de l'organisation
    }

    // Récupère la liste des utilisateurs d'une organisation donnée par son ID
    public function getUsersOrga(int $idOrga): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.organisation = :idOrga')  // Filtre par organisation
            ->setParameter('idOrga', $idOrga)
            ->orderBy('u.id', 'ASC')  // Trie par ID utilisateur
            ->getQuery()
            ->getResult();  // Retourne la liste des utilisateurs
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
