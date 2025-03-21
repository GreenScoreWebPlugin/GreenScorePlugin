<?php

namespace App\Tests\Repository;

use App\Entity\Equivalent;
use App\Repository\EquivalentRepository;
use Doctrine\ORM\Query; 
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class EquivalentRepositoryTest extends TestCase
{
    private EquivalentRepository $equivalentRepository;
    private EntityManagerInterface $entityManager;
    private QueryBuilder $queryBuilder;
    private Query $query; 

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->query = $this->createMock(Query::class); 
        
        $this->equivalentRepository = $this->getMockBuilder(EquivalentRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();
    }

    public function testFindRandomEquivalentsReturnsEmptyArrayWhenNoResults(): void
    {
        // Given
        $count = 3;
        $ratio = 2.5;
        
        $this->equivalentRepository
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('andWhere')
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
        $result = $this->equivalentRepository->findRandomEquivalents($count, $ratio);

        // Then
        $this->assertEmpty($result);
    }

    public function testFindRandomEquivalentsReturnsEquivalents(): void
    {
        // Given
        $count = 2;
        $ratio = 2.5;
        $mockIds = [['id' => 1], ['id' => 2], ['id' => 3]];
        $mockEquivalents = [
            $this->createMock(Equivalent::class),
            $this->createMock(Equivalent::class)
        ];
        
        $this->equivalentRepository
            ->method('createQueryBuilder')
            ->willReturnCallback(function($alias) {
                if ($alias === 'e') {
                    return $this->queryBuilder;
                }
                return null;
            });
            
        $this->queryBuilder
            ->method('select')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('andWhere')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('setParameter')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('where')
            ->willReturn($this->queryBuilder);
            
        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);
            
        // Premier appel pour obtenir les IDs
        $this->query
            ->method('getArrayResult')
            ->willReturn($mockIds);
            
        // Deuxième appel pour obtenir les entités
        $this->query
            ->method('getResult')
            ->willReturn($mockEquivalents);

        // When
        $result = $this->equivalentRepository->findRandomEquivalents($count, $ratio);

        // Then
        $this->assertCount(2, $result);
        $this->assertSame($mockEquivalents, $result);
    }
}