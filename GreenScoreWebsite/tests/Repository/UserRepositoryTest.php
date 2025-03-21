<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Entity\Organisation;
use App\Repository\UserRepository;
use Doctrine\ORM\Query; 
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private QueryBuilder $queryBuilder;
    private Query $query; 
    private Paginator $paginator;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->query = $this->createMock(Query::class); 
        $this->paginator = $this->createMock(Paginator::class);

        $this->userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createQueryBuilder', 'getEntityManager'])
            ->getMock();
            
        $this->userRepository
            ->method('getEntityManager')
            ->willReturn($this->entityManager);
    }

    public function testPaginateUsers(): void
    {
        // Given
        $page = 2;
        $limit = 10;
        $organisationId = 123;
        $offset = ($page - 1) * $limit;

        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setParameter')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setFirstResult')
            ->with($offset)
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setMaxResults')
            ->with($limit)
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('setHint')
            ->willReturn($this->query);

        // When
        $result = $this->userRepository->paginateUsers($page, $limit, $organisationId);

        // Then
        $this->assertInstanceOf(Paginator::class, $result);
    }

    public function testSearchUsersWithoutSearchTerm(): void
    {
        // Given
        $page = 1;
        $limit = 10;
        $organisationId = 123;
        $searchTerm = null;
        $offset = ($page - 1) * $limit;

        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setParameter')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('orderBy')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setFirstResult')
            ->with($offset)
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setMaxResults')
            ->with($limit)
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('setHint')
            ->willReturn($this->query);

        // When
        $result = $this->userRepository->searchUsers($page, $limit, $organisationId, $searchTerm);

        // Then
        $this->assertInstanceOf(Paginator::class, $result);
    }

    public function testSearchUsersWithSearchTerm(): void
    {
        // Given
        $page = 1;
        $limit = 10;
        $organisationId = 123;
        $searchTerm = "John Doe";
        $offset = ($page - 1) * $limit;

        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setParameter')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('orderBy')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setFirstResult')
            ->with($offset)
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setMaxResults')
            ->with($limit)
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('setHint')
            ->willReturn($this->query);

        // When
        $result = $this->userRepository->searchUsers($page, $limit, $organisationId, $searchTerm);

        // Then
        $this->assertInstanceOf(Paginator::class, $result);
    }

    public function testUpgradePasswordWithInvalidUserType(): void
    {
        // Given
        $invalidUser = $this->createMock(\Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface::class);
        $newHashedPassword = 'hashed_password';

        // Then
        $this->expectException(\Symfony\Component\Security\Core\Exception\UnsupportedUserException::class);

        // When
        $this->userRepository->upgradePassword($invalidUser, $newHashedPassword);
    }

    public function testUpgradePasswordWithValidUser(): void
    {
        // Given
        $user = $this->createMock(User::class);
        $newHashedPassword = 'hashed_password';

        $user->expects($this->once())
            ->method('setPassword')
            ->with($newHashedPassword);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($user);

        $this->entityManager->expects($this->once())
            ->method('flush');

        // When
        $this->userRepository->upgradePassword($user, $newHashedPassword);

        // No assertion needed as we're checking method calls
    }

    public function testGetGlobalAverageCarbonFootprintWithNoResults(): void
    {
        // Given
        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getResult')
            ->willReturn([]);

        // When
        $result = $this->userRepository->getGlobalAverageCarbonFootprint();

        // Then
        $this->assertEquals(0.0, $result);
    }

    public function testGetGlobalAverageCarbonFootprintWithResults(): void
    {
        // Given
        $mockResults = [
            ['averageConsumption' => 150.25],
            ['averageConsumption' => 200.75]
        ];

        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getResult')
            ->willReturn($mockResults);

        // Expected: (150.25 + 200.75) / 2 = 175.5, rounded to 175.5
        $expectedAverage = 175.5;

        // When
        $result = $this->userRepository->getGlobalAverageCarbonFootprint();

        // Then
        $this->assertEquals($expectedAverage, $result);
    }

    public function testGetOrganisationGlobalAverageCarbonFootprintWithNoResults(): void
    {
        // Given
        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('groupBy')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getResult')
            ->willReturn([]);

        // When
        $result = $this->userRepository->getOrganisationGlobalAverageCarbonFootprint();

        // Then
        $this->assertEquals(0.0, $result);
    }

    public function testGetOrganisationGlobalAverageCarbonFootprintWithResults(): void
    {
        // Given
        $mockResults = [
            ['averageConsumption' => 300.5, 'organisationId' => 1],
            ['averageConsumption' => 400.8, 'organisationId' => 2],
            ['averageConsumption' => 250.7, 'organisationId' => 3]
        ];

        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('groupBy')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getResult')
            ->willReturn($mockResults);

        // Expected: (300.5 + 400.8 + 250.7) / 3 = 317.33, rounded to 317.33
        $expectedAverage = 317.33;

        // When
        $result = $this->userRepository->getOrganisationGlobalAverageCarbonFootprint();

        // Then
        $this->assertEquals($expectedAverage, $result);
    }

    public function testGetLeastConsumptionCarbonFootprint(): void
    {
        // Given
        $mockResult = ['leastConsumption' => 75.25];

        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getSingleResult')
            ->willReturn($mockResult);

        // When
        $result = $this->userRepository->getLeastConsumptionCarbonFootprint();

        // Then
        $this->assertEquals(75.25, $result);
    }

    public function testGetOrganisationLeastConsumptionCarbonFootprintWithNoResults(): void
    {
        // Given
        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('groupBy')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('orderBy')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setMaxResults')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getOneOrNullResult')
            ->willReturn(null);

        // When
        $result = $this->userRepository->getOrganisationLeastConsumptionCarbonFootprint();

        // Then
        $this->assertEquals(0.0, $result);
    }

    public function testGetOrganisationLeastConsumptionCarbonFootprintWithResults(): void
    {
        // Given
        $mockResult = ['totalConsumption' => 500.75, 'organisationId' => 1];

        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('groupBy')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('orderBy')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setMaxResults')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getOneOrNullResult')
            ->willReturn($mockResult);

        // When
        $result = $this->userRepository->getOrganisationLeastConsumptionCarbonFootprint();

        // Then
        $this->assertEquals(500.75, $result);
    }

    public function testGetUsersOrga(): void
    {
        // Given
        $organisationId = 123;
        $mockUsers = [
            $this->createMock(User::class),
            $this->createMock(User::class)
        ];

        $this->userRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('setParameter')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('orderBy')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getResult')
            ->willReturn($mockUsers);

        // When
        $result = $this->userRepository->getUsersOrga($organisationId);

        // Then
        $this->assertCount(2, $result);
        $this->assertSame($mockUsers, $result);
    }
}