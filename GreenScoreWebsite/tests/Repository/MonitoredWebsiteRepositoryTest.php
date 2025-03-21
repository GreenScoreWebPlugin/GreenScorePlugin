<?php

namespace App\Tests\Repository;

use App\Entity\MonitoredWebsite;
use App\Repository\MonitoredWebsiteRepository;
use Doctrine\ORM\Query; 
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class MonitoredWebsiteRepositoryTest extends TestCase
{
    private MonitoredWebsiteRepository $monitoredWebsiteRepository;
    private EntityManagerInterface $entityManager;
    private QueryBuilder $queryBuilder;
    private Query $query; 

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->query = $this->createMock(Query::class); 
        
        $this->monitoredWebsiteRepository = $this->getMockBuilder(MonitoredWebsiteRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();
    }

    public function testFindLastAddedByUserReturnsWebsite(): void
    {
        // Given
        $userId = 42;
        $mockWebsite = $this->createMock(MonitoredWebsite::class);
        
        $this->monitoredWebsiteRepository
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
            ->method('setMaxResults')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);
            
        $this->query
            ->method('getOneOrNullResult')
            ->willReturn($mockWebsite);

        // When
        $result = $this->monitoredWebsiteRepository->findLastAddedByUser($userId);

        // Then
        $this->assertSame($mockWebsite, $result);
    }

    public function testGetAverageDailyCarbonFootprintReturnsZeroWhenNoData(): void
    {
        // Given
        $usersIds = [42];
        
        $this->monitoredWebsiteRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('setParameter')
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
        $result = $this->monitoredWebsiteRepository->getAverageDailyCarbonFootprint($usersIds);

        // Then
        $this->assertEquals(0.0, $result);
    }

    public function testGetAverageDailyCarbonFootprintCalculatesAverage(): void
    {
        // Given
        $usersIds = [42];
        $dailyAverages = [
            ['day' => '2025-03-18', 'dailyAverage' => 10.5],
            ['day' => '2025-03-19', 'dailyAverage' => 8.7],
            ['day' => '2025-03-20', 'dailyAverage' => 12.3]
        ];
        
        $this->monitoredWebsiteRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('setParameter')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('groupBy')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);
            
        $this->query
            ->method('getResult')
            ->willReturn($dailyAverages);

        // When
        $result = $this->monitoredWebsiteRepository->getAverageDailyCarbonFootprint($usersIds);

        // Then
        // Expected: (10.5 + 8.7 + 12.3) / 3 = 10.50 (rounded to 2 decimals)
        $this->assertEquals(10.50, $result);
    }

    public function testGetTop5PollutingSitesByUsersReturnsSites(): void
    {
        // Given
        $usersIds = [1, 2, 3];
        $expectedTop5 = [
            ['urlDomain' => 'example.com', 'totalFootprint' => 150.5],
            ['urlDomain' => 'test.org', 'totalFootprint' => 120.3]
        ];
        
        $this->monitoredWebsiteRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('setParameter')
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
            ->method('getResult')
            ->willReturn($expectedTop5);

        // When
        $result = $this->monitoredWebsiteRepository->getTop5PollutingSitesByUsers($usersIds);

        // Then
        $this->assertSame($expectedTop5, $result);
    }

    public function testGetConsuByFilterWithDayFilter(): void
    {
        // Given
        $usersIds = [1, 2, 3];
        $filter = 'jour';
        $expectedResults = [
            ['period' => '2025-03-18', 'total_consumption' => 120.5],
            ['period' => '2025-03-19', 'total_consumption' => 130.3]
        ];
        
        $this->monitoredWebsiteRepository
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
            ->method('setParameter')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('groupBy')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);
            
        $this->query
            ->method('getResult')
            ->willReturn($expectedResults);

        // When
        $result = $this->monitoredWebsiteRepository->getConsuByFilter($usersIds, $filter);

        // Then
        $this->assertSame($expectedResults, $result);
    }
}