<?php
// src/Service/EquivalentCalculator.php

namespace App\Service;

use App\Repository\EquivalentRepository;
use Exception;

/*!
 * Cette classe permet de calculer les équivalents CO2 en fonction de la consommation des utilisateurs.
 */
class EquivalentCalculatorService
{
    public function __construct(
        private EquivalentRepository $equivalentRepository
    ) {}

    public function calculateEquivalents(float $gCo2, int $count): array
    {
        // Conversion des grammes en kilogrammes (1 kg = 1000 g)
        $kgCo2 = $gCo2 / 1000;

        // Base de comparaison : 100 kg de CO2
        $baseKgCo2 = 100;

        // Calcul du ratio de consommation par rapport à 100 kg de CO2
        $ratio = $kgCo2 / $baseKgCo2;

        // Récupération d'un nombre aléatoire d'équivalents, selon le ratio
        try {
            $equivalents = $this->equivalentRepository->findRandomEquivalents($count, $ratio);
        } catch (Exception $e) {
            $this->logger->error('Erreur lors de la récupération des équivalents : ' . $e->getMessage());
            return ['error' => 'Impossible de récupérer les équivalents à ce moment.'];
        }
        

        // Initialisation du tableau de réponse
        $response = [];

        // Parcours des équivalents récupérés pour calculer la valeur en fonction du ratio
        foreach ($equivalents as $equivalent) {
            // Multiplie la valeur d'équivalence par le ratio de consommation
            $equivalentValue = $ratio * $equivalent->getEquivalent();
            
            // Ajout du résultat dans le tableau de réponse avec le nom, la valeur et l'icône
            $response[] = [
                'name' => $equivalent->getName(), // Nom de l'équivalent (par exemple, "Arbre planté")
                'value' => round($equivalentValue, 2), // Valeur arrondie à 2 décimales
                'icon' => $equivalent->getIconThumbnail(), // Icône associée à l'équivalent
            ];
        }

        // Retourne le tableau avec tous les équivalents calculés
        return $response;
    }

}