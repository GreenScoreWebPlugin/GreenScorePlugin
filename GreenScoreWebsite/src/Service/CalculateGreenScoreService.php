<?php
// src/Service/EquivalentCalculator.php

namespace App\Service;

use App\Repository\MonitoredWebsiteRepository;

class CalculateGreenScoreService 
{
    public function __construct(
        private MonitoredWebsiteRepository $MonitoredWebsiteRepository
    ) {}

    public function calculateGreenScore(): array
    {
        // Consommation moyenne de l'ensemble des utilisateurs du site et consommateur le plus faible
        $averageConsumption = $monitoredWebsiteRepository->getGlobalAverageCarbonFootprint();
        $leastConsumption = $monitoredWebsiteRepository->getLeastConsumptionCarbonFootprint();

        if($averageConsumption && $leastConsumption && $averageConsumption > 0 && $totalConsu != null) {
            $maxConsumption = ($averageConsumption - $leastConsumption) * 2;
            $scale = ($maxConsumption - $leastConsumption) / 7;
            try {
                switch ($totalConsu) {
                    case $totalConsu < $leastConsumption + $scale :
                        $envNomination = 'Gardien des Écosystèmes';
                        $letterGreenScore = 'A';
                        break;
                    case $totalConsu >= $leastConsumption + $scale && $totalConsu < $leastConsumption + $scale * 2:
                        $envNomination = 'Allié de la Nature';
                        $letterGreenScore = 'B';
                        break;
                    case $totalConsu >= $leastConsumption + $scale * 2 && $totalConsu < $leastConsumption + $scale * 3:
                        $envNomination = 'Explorateur Prudent';
                        $letterGreenScore = 'C';
                        break;
                    case $totalConsu >= $leastConsumption + $scale * 3 && $totalConsu < $leastConsumption + $scale * 4:
                        $envNomination = 'Voyageur Insouciant';
                        $letterGreenScore = 'D';
                        break;
                    case $totalConsu >= $leastConsumption + $scale * 4 && $totalConsu < $leastConsumption + $scale * 5:
                        $envNomination = 'Consommateur Dynamique';
                        $letterGreenScore = 'E';
                        break;
                    case $totalConsu >= $leastConsumption + $scale * 5 && $totalConsu < $leastConsumption + $scale * 6:
                        $envNomination = 'Exploitant Intense';
                        $letterGreenScore = 'F';
                        break;
                    case $totalConsu >= $leastConsumption + $scale * 7:
                        $envNomination = 'Grand Consommateur';
                        $letterGreenScore = 'G';
                        break;
                    default:
                        $envNomination = null;
                        break;
                }

            } catch (Exception $e) {
                $this->logger->error('Erreur lors de la récupération du badge : ' . $e->getMessage());
                $letterGreenScore = null;
            }
        }


        $response[] = [
            'envNomination' => $envNomination ?? null,
            'letterGreenScore' => $letterGreenScore ?? null
        ];

        return $response;
    }
}