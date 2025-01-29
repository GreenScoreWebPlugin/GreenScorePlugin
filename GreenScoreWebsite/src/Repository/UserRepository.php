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

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function paginateUsers(int $page, int $limit, int $organisationId): Paginator
    {
        return new Paginator($this
            ->createQueryBuilder('u')
            ->andWhere('u.organisation = :organisation')
            ->setParameter('organisation', $organisationId)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
            false
        );
    }

    public function searchUsers(int $page, int $limit, int $organisationId, ?string $searchTerm = null): Paginator
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.organisation = :organisation')
            ->setParameter('organisation', $organisationId)
            ->orderBy('u.lastName', 'ASC');

        if ($searchTerm && trim($searchTerm) !== '') {
            $searchTerm = trim($searchTerm);
            $qb->andWhere('
            LOWER(u.firstName) LIKE LOWER(:searchTerm) OR 
            LOWER(u.lastName) LIKE LOWER(:searchTerm) OR 
            LOWER(u.email) LIKE LOWER(:searchTerm)
        ')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $firstResult = ($page - 1) * $limit;

        $query = $qb->setFirstResult($firstResult)
            ->setMaxResults($limit)
            ->getQuery()
            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false);

        return new Paginator($query, false);
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
