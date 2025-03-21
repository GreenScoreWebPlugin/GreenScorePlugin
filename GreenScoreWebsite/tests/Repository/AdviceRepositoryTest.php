<?php

namespace App\Tests\Repository;

use App\Entity\Advice;
use App\Repository\AdviceRepository;
use Doctrine\ORM\Query; 
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class AdviceRepositoryTest extends TestCase
{
    private AdviceRepository $adviceRepository;
    private EntityManagerInterface $entityManager;
    private QueryBuilder $queryBuilder;
    private Query $query; 

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->query = $this->createMock(Query::class); 
        
        $this->adviceRepository = $this->getMockBuilder(AdviceRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createQueryBuilder', 'find'])
            ->getMock();
    }

    public function testFindRandomByIsDevReturnsNullWhenNoResults(): void
    {
        // Given
        $isDev = true;
        
        $this->adviceRepository
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
            ->method('getQuery')
            ->willReturn($this->query);
            
        $this->query
            ->method('getArrayResult')
            ->willReturn([]);

        // When
        $result = $this->adviceRepository->findRandomByIsDev($isDev);

        // Then
        $this->assertNull($result);
    }

    public function testFindRandomByIsDevReturnsAdvice(): void
    {
        // Given
        $isDev = true;
        $mockAdvice = $this->createMock(Advice::class);
        $mockIds = [['id' => 1], ['id' => 2], ['id' => 3]];
        
        $this->adviceRepository
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
            ->method('getQuery')
            ->willReturn($this->query);
            
        $this->query
            ->method('getArrayResult')
            ->willReturn($mockIds);
            
        $this->adviceRepository
            ->method('find')
            ->willReturn($mockAdvice);

        // When
        $result = $this->adviceRepository->findRandomByIsDev($isDev);

        // Then
        $this->assertSame($mockAdvice, $result);
    }
}