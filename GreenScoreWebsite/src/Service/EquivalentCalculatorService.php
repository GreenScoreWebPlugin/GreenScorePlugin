<?php
// src/Service/EquivalentCalculator.php

namespace App\Service;

use App\Repository\EquivalentRepository;

class EquivalentCalculatorService
{
    public function __construct(
        private EquivalentRepository $equivalentRepository
    ) {}

    public function calculateEquivalents(float $gCo2, int $count): array
    {
        // Conversion en kg
        $kgCo2 = $gCo2 / 1000;
        
        $equivalents = $this->equivalentRepository->findRandomEquivalents($count);
        
        $response = [];
        foreach ($equivalents as $equivalent) {
            // On utilise 100kg CO2 comme base de référence
            $baseKgCo2 = 100;
            
            // On calcule combien de fois notre consommation contient 100kg
            $ratio = $kgCo2 / $baseKgCo2;
            
            // On multiplie la valeur d'équivalence par ce ratio
            $equivalentValue = $ratio * $equivalent->getEquivalent();
            
            $response[] = [
                'name' => $equivalent->getName(),
                'value' => round($equivalentValue),
                'icon' => $equivalent->getIconThumbnail(),
            ];
        }

        return $response;
    }
}