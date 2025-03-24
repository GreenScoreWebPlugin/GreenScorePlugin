<?php
// src/Service/CalculateGreenScoreService.php

namespace App\Service;

use App\Repository\UserRepository;
use Psr\Log\LoggerInterface; // Ajout pour logger les erreurs
use Exception;

class CalculateGreenScoreService
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function calculateGreenScore(float $totalConsu, string $page): array
    {
        $envNomination = null;
        $letterGreenScore = null;

        // Consommation moyenne de l'ensemble des utilisateurs du site et consommateur le plus faible
        if ($page == 'mon-organisation' || $page == 'mes-donnees') {
            if ($page == 'mon-organisation') {
                $averageConsumption = $this->userRepository->getOrganisationGlobalAverageCarbonFootprint();
                $leastConsumption = $this->userRepository->getOrganisationLeastConsumptionCarbonFootprint();
            }
            if ($page == 'mes-donnees') {
                $averageConsumption = $this->userRepository->getGlobalAverageCarbonFootprint();
                $leastConsumption = $this->userRepository->getLeastConsumptionCarbonFootprint();
            }

            // Vérification des valeurs nécessaires avant de calculer
            if ($averageConsumption && $leastConsumption && $averageConsumption > 0 && $totalConsu !== null) {
                $maxConsumption = ($averageConsumption - $leastConsumption) * 2;
                $scale = ($maxConsumption - $leastConsumption) / 7;

                try {
                    switch (true) {
                        case ($totalConsu < $leastConsumption + $scale):
                            $envNomination = 'Gardien des Écosystèmes';
                            $letterGreenScore = 'A';
                            break;
                        case ($totalConsu >= $leastConsumption + $scale && $totalConsu < $leastConsumption + $scale * 2):
                            $envNomination = 'Allié de la Nature';
                            $letterGreenScore = 'B';
                            break;
                        case ($totalConsu >= $leastConsumption + $scale * 2 && $totalConsu < $leastConsumption + $scale * 3):
                            $envNomination = 'Explorateur Prudent';
                            $letterGreenScore = 'C';
                            break;
                        case ($totalConsu >= $leastConsumption + $scale * 3 && $totalConsu < $leastConsumption + $scale * 4):
                            $envNomination = 'Voyageur Insouciant';
                            $letterGreenScore = 'D';
                            break;
                        case ($totalConsu >= $leastConsumption + $scale * 4 && $totalConsu < $leastConsumption + $scale * 5):
                            $envNomination = 'Consommateur Dynamique';
                            $letterGreenScore = 'E';
                            break;
                        case ($totalConsu >= $leastConsumption + $scale * 5 && $totalConsu < $leastConsumption + $scale * 6):
                            $envNomination = 'Exploitant Intense';
                            $letterGreenScore = 'F';
                            break;
                        case ($totalConsu >= $leastConsumption + $scale * 7):
                            $envNomination = 'Grand Consommateur';
                            $letterGreenScore = 'G';
                            break;
                        default:
                            $envNomination = null;
                            $letterGreenScore = null;
                            break;
                    }
                } catch (Exception $e) {
                    // Gestion des erreurs avec logging
                    $this->logger->error('Erreur lors de la récupération du badge GreenScore : ' . $e->getMessage());
                    $envNomination = null;
                    $letterGreenScore = null;
                }
            }

            $response[] = [
                'envNomination' => $envNomination ?? null,
                'letterGreenScore' => $letterGreenScore ?? null
            ];

            return $response;
        } else {
            try {
                $echelle = 0.25;

                switch (true) {
                    case ($totalConsu < $echelle):
                        $envNomination = 'Maître des Forêts';
                        $letterGreenScore = 'A';
                        break;
                    case ($totalConsu > $echelle && $totalConsu < $echelle*2):
                        $envNomination = 'Protecteur des Bois';
                        $letterGreenScore = 'B';
                        break;
                    case ($totalConsu > $echelle*2 && $totalConsu < $echelle*3):
                        $envNomination = 'Frère des Arbres';
                        $letterGreenScore = 'C';
                        break;
                    case ($totalConsu > $echelle*3 && $totalConsu < $echelle*4):
                        $envNomination = 'Initié de la Nature';
                        $letterGreenScore = 'D';
                        break;
                    case ($totalConsu > $echelle*4 && $totalConsu < $echelle*5):
                        $envNomination = 'Explorateur Imprudent';
                        $letterGreenScore = 'E';
                        break;
                    case ($totalConsu > $echelle*5 && $totalConsu < $echelle*6):
                        $envNomination = 'Tempête Numérique';
                        $letterGreenScore = 'F';
                        break;
                    case ($totalConsu > $echelle*6):
                        $envNomination = 'Bouleverseur des Écosystèmes';
                        $letterGreenScore = 'G';
                        break;
                    default:
                        $envNomination = null;
                        $letterGreenScore = null;
                        break;
                }
                $response[] = [
                    'envNomination' => $envNomination ?? null,
                    'letterGreenScore' => $letterGreenScore ?? null
                ];
                return $response;

            } catch (Exception $e) {
                $this->logger->error('Erreur lors de la récupération du badge GreenScore : ' . $e->getMessage());
                $letterEcoIndex = null;
            }
        }
    }
}