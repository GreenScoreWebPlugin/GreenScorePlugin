<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CalculateGreenScoreService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CalculateGreenScoreServiceTest extends TestCase
{
    private CalculateGreenScoreService $calculateGreenScoreService;
    private UserRepository $userRepository;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->calculateGreenScoreService = new CalculateGreenScoreService(
            $this->userRepository,
            $this->logger
        );
    }

    /**
     * @dataProvider provideOrganisationData
     */
    public function testCalculateGreenScoreForOrganisation(
        float $totalConsumption,
        float $averageConsumption,
        float $leastConsumption,
        string $expectedNomination,
        string $expectedLetter
    ): void {
        // Given
        $this->userRepository
            ->method('getOrganisationGlobalAverageCarbonFootprint')
            ->willReturn((float)$averageConsumption);
        
        $this->userRepository
            ->method('getOrganisationLeastConsumptionCarbonFootprint')
            ->willReturn((float)$leastConsumption);

        // When
        $result = $this->calculateGreenScoreService->calculateGreenScore($totalConsumption, 'mon-organisation');

        // Then
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals($expectedNomination, $result[0]['envNomination']);
        $this->assertEquals($expectedLetter, $result[0]['letterGreenScore']);
    }

    /**
     * @dataProvider providePersonalDataData
     */
    public function testCalculateGreenScoreForPersonalData(
        float $totalConsumption,
        float $averageConsumption,
        float $leastConsumption,
        string $expectedNomination,
        string $expectedLetter
    ): void {
        // Given
        $this->userRepository
            ->method('getGlobalAverageCarbonFootprint')
            ->willReturn((float)$averageConsumption);
        
        $this->userRepository
            ->method('getLeastConsumptionCarbonFootprint')
            ->willReturn((float)$leastConsumption);

        // When
        $result = $this->calculateGreenScoreService->calculateGreenScore($totalConsumption, 'mes-donnees');

        // Then
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals($expectedNomination, $result[0]['envNomination']);
        $this->assertEquals($expectedLetter, $result[0]['letterGreenScore']);
    }

    /**
     * @dataProvider provideGenericData
     */
    public function testCalculateGreenScoreForGenericPage(
        float $totalConsumption,
        string $expectedNomination,
        string $expectedLetter
    ): void {
        // Given
        $page = 'autre-page';

        // When
        $result = $this->calculateGreenScoreService->calculateGreenScore($totalConsumption, $page);

        // Then
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals($expectedNomination, $result[0]['envNomination']);
        $this->assertEquals($expectedLetter, $result[0]['letterGreenScore']);
    }

    public function testCalculateGreenScoreReturnsNullWhenMissingRepositoryData(): void
    {
        // Given
        $this->userRepository
            ->method('getOrganisationGlobalAverageCarbonFootprint')
            ->willReturn((float)0);
        
        $this->userRepository
            ->method('getOrganisationLeastConsumptionCarbonFootprint')
            ->willReturn((float)0);

        // When
        $result = $this->calculateGreenScoreService->calculateGreenScore(100, 'mon-organisation');

        // Then
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertNull($result[0]['envNomination']);
        $this->assertNull($result[0]['letterGreenScore']);
    }

    public function testCalculateGreenScoreHandlesExceptionGracefully(): void
    {
        // Given
        $this->userRepository
            ->method('getOrganisationGlobalAverageCarbonFootprint')
            ->willThrowException(new \Exception('Database error'));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Erreur lors de la récupération du badge GreenScore'));

        // When
        $result = $this->calculateGreenScoreService->calculateGreenScore(100, 'mon-organisation');

        // Then
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertNull($result[0]['envNomination']);
        $this->assertNull($result[0]['letterGreenScore']);
    }

    public function provideOrganisationData(): array
    {
        return [
            'Grade A' => [30, 100, 20, 'Gardien des Écosystèmes', 'A'], 
            'Grade B' => [50, 100, 20, 'Allié de la Nature', 'B'], 
            'Grade C' => [70, 100, 20, 'Explorateur Prudent', 'C'], 
            'Grade D' => [90, 100, 20, 'Voyageur Insouciant', 'D'], 
            'Grade E' => [110, 100, 20, 'Consommateur Dynamique', 'E'], 
            'Grade F' => [130, 100, 20, 'Exploitant Intense', 'F'], 
            'Grade G' => [150, 100, 20, 'Grand Consommateur', 'G'],
        ];
    }

    public function providePersonalDataData(): array
    {
        return [
            'Grade A' => [30, 100, 20, 'Gardien des Écosystèmes', 'A'], 
            'Grade B' => [50, 100, 20, 'Allié de la Nature', 'B'], 
            'Grade C' => [70, 100, 20, 'Explorateur Prudent', 'C'], 
            'Grade D' => [90, 100, 20, 'Voyageur Insouciant', 'D'], 
            'Grade E' => [110, 100, 20, 'Consommateur Dynamique', 'E'],
            'Grade F' => [130, 100, 20, 'Exploitant Intense', 'F'], 
            'Grade G' => [150, 100, 20, 'Grand Consommateur', 'G'],
        ];
    }

    public function provideGenericData(): array
    {
        return [
            'Grade A' => [0.2, 'Maître des Forêts', 'A'],
            'Grade B' => [0.3, 'Protecteur des Bois', 'B'],
            'Grade C' => [0.6, 'Frère des Arbres', 'C'],
            'Grade D' => [0.9, 'Initié de la Nature', 'D'],
            'Grade E' => [1.2, 'Explorateur Imprudent', 'E'],
            'Grade F' => [1.4, 'Tempête Numérique', 'F'],
            'Grade G' => [1.6, 'Bouleverseur des Écosystèmes', 'G'],
        ];
    }
}