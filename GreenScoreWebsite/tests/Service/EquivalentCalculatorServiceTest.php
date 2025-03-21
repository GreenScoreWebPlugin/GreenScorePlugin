<?php

namespace App\Tests\Service;

use App\Entity\Equivalent;
use App\Repository\EquivalentRepository;
use App\Service\EquivalentCalculatorService;
use PHPUnit\Framework\TestCase;

class EquivalentCalculatorServiceTest extends TestCase
{
    private EquivalentRepository $equivalentRepository;
    private EquivalentCalculatorService $equivalentCalculatorService;

    protected function setUp(): void
    {
        $this->equivalentRepository = $this->createMock(EquivalentRepository::class);
        $this->equivalentCalculatorService = new EquivalentCalculatorService($this->equivalentRepository);
    }

    public function testCalculateEquivalentsReturnsEmptyArrayWhenNoEquivalentsFound(): void
    {
        // Given - n'importe quelle valeur de CO2 et nombre d'équivalents
        $gCo2 = 250000;
        $count = 3;
        $ratio = 2.5; // 250kg / 100kg base = 2.5

        // Simuler un retour vide du repository
        $this->equivalentRepository
            ->method('findRandomEquivalents')
            ->willReturn([]);

        // When
        $result = $this->equivalentCalculatorService->calculateEquivalents($gCo2, $count);

        // Then - vérifier que le résultat est un tableau vide
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testCalculateEquivalentsReturnsProperlyFormattedResults(): void
    {
        // Given - n'importe quelle valeur de CO2 et nombre d'équivalents
        $gCo2 = 250000;
        $count = 2;
        $ratio = 2.5; // 250kg / 100kg base = 2.5

        // Créer des mocks d'équivalents avec des valeurs arbitraires
        $equivalent1 = $this->createMock(Equivalent::class);
        $equivalent1->method('getName')->willReturn('Mock Equivalent 1');
        $equivalent1->method('getEquivalent')->willReturn(10.0);
        $equivalent1->method('getIconThumbnail')->willReturn('icon1.png');

        $equivalent2 = $this->createMock(Equivalent::class);
        $equivalent2->method('getName')->willReturn('Mock Equivalent 2');
        $equivalent2->method('getEquivalent')->willReturn(5.0);
        $equivalent2->method('getIconThumbnail')->willReturn('icon2.png');

        $mockEquivalents = [$equivalent1, $equivalent2];

        // Configurer le mock du repository pour retourner nos équivalents simulés
        $this->equivalentRepository
            ->method('findRandomEquivalents')
            ->willReturn($mockEquivalents);

        // When
        $result = $this->equivalentCalculatorService->calculateEquivalents($gCo2, $count);

        // Then
        $this->assertIsArray($result);
        $this->assertCount(count($mockEquivalents), $result);
        
        // Vérifier seulement la structure et les types, pas les valeurs spécifiques
        foreach ($result as $index => $item) {
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('icon', $item);
            
            $this->assertIsString($item['name']);
            $this->assertIsNumeric($item['value']);
            $this->assertIsString($item['icon']);
            
            // Vérifier que le calcul est correct sans supposer quels équivalents sont retournés
            $expectedValue = $ratio * $mockEquivalents[$index]->getEquivalent();
            $this->assertEquals($expectedValue, $item['value']);
        }
    }

    public function testCalculateEquivalentsHandlesZeroValue(): void
    {
        // Given
        $gCo2 = 0;
        $count = 2;
        
        // Créer quelques équivalents arbitraires
        $equivalent1 = $this->createMock(Equivalent::class);
        $equivalent1->method('getName')->willReturn('Mock Equivalent');
        $equivalent1->method('getEquivalent')->willReturn(10.0);
        $equivalent1->method('getIconThumbnail')->willReturn('icon.png');

        // Configurer le mock du repository
        $this->equivalentRepository
            ->method('findRandomEquivalents')
            ->willReturn([$equivalent1]);

        // When
        $result = $this->equivalentCalculatorService->calculateEquivalents($gCo2, $count);

        // Then
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        
        // Vérifier que toutes les valeurs sont à zéro quand gCo2 = 0
        foreach ($result as $item) {
            $this->assertEquals(0, $item['value']);
        }
    }

    public function testCalculateEquivalentsAppliesRatioCorrectly(): void
    {
        // Given - différentes valeurs de CO2 pour tester différents ratios
        $testCases = [
            ['gCo2' => 10000, 'expectedRatio' => 0.1], // 10kg / 100kg = 0.1
            ['gCo2' => 100000, 'expectedRatio' => 1.0], // 100kg / 100kg = 1.0
            ['gCo2' => 1000000, 'expectedRatio' => 10.0], // 1000kg / 100kg = 10.0
        ];
        
        foreach ($testCases as $testCase) {
            $gCo2 = $testCase['gCo2'];
            $expectedRatio = $testCase['expectedRatio'];
            $count = 1;
            
            // Créer un équivalent avec une valeur connue
            $equivalent = $this->createMock(Equivalent::class);
            $equivalent->method('getName')->willReturn('Mock Equivalent');
            $equivalent->method('getEquivalent')->willReturn(1.0); // Valeur 1.0 pour faciliter le test
            $equivalent->method('getIconThumbnail')->willReturn('icon.png');
            
            // Configurer le mock du repository
            $this->equivalentRepository
                ->method('findRandomEquivalents')
                ->willReturn([$equivalent]);
            
            // When
            $result = $this->equivalentCalculatorService->calculateEquivalents($gCo2, $count);
            
            // Then
            $this->assertCount(1, $result);
            
            // Avec un équivalent de valeur 1.0, la valeur résultante devrait être égale au ratio
            $this->assertEquals($expectedRatio, $result[0]['value']);
        }
    }
}