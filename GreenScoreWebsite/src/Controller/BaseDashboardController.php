<?php

namespace App\Controller;

use App\Repository\MonitoredWebsiteRepository;
use App\Service\EquivalentCalculatorService;
use App\Service\CalculateGreenScoreService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

/*!
 * Cette classe est un controller qui contient toutes les routes de notre API afin de pouvoir les appeler depuis
 * le frontend de l'application Web, mais aussi depuis le plugin.
 */
class BaseDashboardController extends AbstractController
{
    protected HttpClientInterface $httpClient; ///< Stocke l'objet HttpClientInterface qui permet de faire des requêtes HTTP
    protected LoggerInterface $logger; ///< Stocke l'objet LoggerInterface qui permet de logger les erreurs
    protected EquivalentCalculatorService $equivalentCalculatorService; ///< Stocke l'objet EquivalentCalculatorService qui permet de calculer les équivalents
    protected CalculateGreenScoreService $calculateGreenScoreService; ///< Stocke l'objet CalculateGreenScoreService qui permet de calculer le GreenScore

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, EquivalentCalculatorService $equivalentCalculatorService, CalculateGreenScoreService $calculateGreenScoreService)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->equivalentCalculatorService = $equivalentCalculatorService;
        $this->calculateGreenScoreService = $calculateGreenScoreService;
    }

    protected function formatConsumption(?float $totalConsu): string
    {
        if ($totalConsu === null) {
            return 'N/A';
        }

        if ($totalConsu >=1000000) {
            $value = $totalConsu / 1000000;
        } elseif ($totalConsu >= 1000) {
            $value = $totalConsu / 1000;
        } else {
            $value = $totalConsu;
        }

        // Ajout de décimales uniquement pour les petites valeurs (<10)
        $formattedValue = $value < 10
            ? number_format($value, 2, '.', ' ') // 2 décimales pour les petites valeurs
            : number_format($value, 0, '.', ' '); // Pas de décimales pour les autres

        return $formattedValue;
    }

    protected function formatUnitConsumption(?float $totalConsu): string
    {

        if ($totalConsu >=1000000) {
            $unit = 'TCO2e'; // Tonne métrique
        } elseif ($totalConsu >= 1000) {
            $unit = 'kgCO2e'; // Kilogramme
        } else {
            $unit = 'gCO2e'; // Gramme
        }
        return $unit;
    }

    protected function formatSize(?float $size): string
    {
        if ($size === null) {
            return 'N/A';
        }

        if ($size >= 1099511627776) { // 1 To = 2^40 octets
            $value = $size / 1099511627776;
        } elseif ($size >= 1073741824) { // 1 Go = 2^30 octets
            $value = $size / 1073741824;
        } elseif ($size >= 1048576) { // 1 Mo = 2^20 octets
            $value = $size / 1048576;
        } elseif ($size >= 1024) { // 1 Ko = 2^10 octets
            $value = $size / 1024;
        } else {
            $value = $size;
        }

        // Ajout de décimales uniquement pour les petites valeurs (<10)
        $formattedValue = $value < 10
            ? number_format($value, 2, '.', ' ') // 2 décimales pour les petites valeurs
            : number_format($value, 0, '.', ' '); // Pas de décimales pour les autres

        return round($formattedValue, 1);
    }

    protected function formatUnitSize(?float $size): string
    {
        if ($size === null) {
            return 'N/A';
        }

        if ($size >= 1099511627776) { // 1 To = 2^40 octets
            $unit = 'To'; // Téraoctets
        } elseif ($size >= 1073741824) { // 1 Go = 2^30 octets
            $unit = 'Go'; // Gigaoctets
        } elseif ($size >= 1048576) { // 1 Mo = 2^20 octets
            $unit = 'Mo'; // Mégaoctets
        } elseif ($size >= 1024) { // 1 Ko = 2^10 octets
            $unit = 'Ko'; // Kilooctets
        } else {
            $unit = 'octets'; // Octets
        }

        return $unit;
    }

    #[Route('/api/{filter}-consu', name: 'consu_user')]
    public function getConsuByUser(Request $request, MonitoredWebsiteRepository $repository, string $filter): JsonResponse
    {
        $usersIdsString = $request->query->get('usersIds', '');
        $usersIds = array_filter(explode(',', $usersIdsString), fn($id) => is_numeric($id));

        if (empty($usersIds)) {
            return $this->json(['error' => 'La liste des utilisateurs est vide ou invalide'], 400);
        }

        $date = new DateTime();
        $consuData = $repository->getConsuByFilter($usersIds, $filter);
        
        // Préparation des périodes et données
        switch ($filter) {
            case 'jour':
                return $this->formatDailyData($consuData, $date);
            case 'semaine':
                return $this->formatWeeklyData($consuData, $date);
            case 'mois':
                return $this->formatMonthlyData($consuData, $date);
            default:
                return $this->json(['error' => 'Filtre invalide'], 400);
        }
    }

    protected function formatDailyData(array $consuData, DateTime $currentDate): JsonResponse
    {
        $labels = [];
        $data = [];
        
        // Créer un tableau associatif pour un accès rapide aux données
        $consumptionByDate = [];
        foreach ($consuData as $item) {
            $consumptionByDate[$item['period']] = $item['total_consumption'];
        }
        
        // Générer les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $date = (clone $currentDate)->modify("-$i days");
            $formattedDate = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $data[] = $consumptionByDate[$formattedDate] ?? 0;
        }
        
        return $this->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    protected function formatWeeklyData(array $consuData, DateTime $currentDate): JsonResponse
    {
        $labels = ['s-3', 's-2', 's-1', 's'];
        $data = array_fill(0, 4, 0);
        
        // On définit le début de la semaine courante
        $currentWeekStart = (clone $currentDate)->modify('monday this week');
        
        // On crée un mapping des dates de début de chaque semaine
        $weekStarts = [];
        for ($i = 0; $i < 4; $i++) {
            $weekStart = (clone $currentWeekStart)->modify("-{$i} weeks");
            $weekStarts[$i] = [
                'start' => clone $weekStart,
                'end' => (clone $weekStart)->modify('sunday this week')
            ];
        }

        // On traite chaque entrée de données
        foreach ($consuData as $item) {
            $date = new DateTime($item['period']);
            
            // On cherche dans quelle semaine tombe cette date
            foreach ($weekStarts as $weekIndex => $weekDates) {
                if ($date >= $weekDates['start'] && $date <= $weekDates['end']) {
                    $displayIndex = 3 - $weekIndex; // Converti l'index pour l'affichage (0 => s, 1 => s-1, etc.)
                    $data[$displayIndex] = $item['total_consumption'];
                    break;
                }
            }
        }
        
        return $this->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    protected function formatMonthlyData(array $consuData, DateTime $currentDate): JsonResponse
    {
        $labels = [];
        $data = array_fill(0, 12, 0);
        $frenchMonths = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        
        // Créer un mapping des périodes
        $periodMap = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = (clone $currentDate)->modify("-$i months");
            $month = (int)$date->format('n');
            $year = (int)$date->format('Y');
            $labels[] = $frenchMonths[$month - 1] . ' ' . $year;
            $periodMap[] = ['month' => $month, 'year' => $year];
        }
        
        // Mapper les données aux mois correspondants
        foreach ($consuData as $item) {
            foreach ($periodMap as $index => $period) {
                if ($item['month'] === $period['month'] && $item['year'] === $period['year']) {
                    $data[$index] = $item['total_consumption'];
                    break;
                }
            }
        }
        
        return $this->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    #[Route('/api/top-sites', name: 'top_sites_organisation')]
    public function getTopSitesByOrganisation(Request $request, MonitoredWebsiteRepository $repository): JsonResponse
    {
        $usersIdsString = $request->query->get('usersIds', '');

        $usersIds = array_filter(explode(',', $usersIdsString), fn($id) => is_numeric($id));

        if (empty($usersIds)) {
            return $this->json(['error' => 'La liste des utilisateurs est vide ou invalide'], 400);
        }

        $top5Sites = $repository->getTop5PollutingSitesByUsers($usersIds);
        
        return $this->json(array_map(fn($site) => [
            $site['urlDomain'],
            round((float)$site['totalFootprint'], 2)
        ], $top5Sites));
    }

    #[Route('/api/equivalent', name: 'get_equivalent', methods: ['GET'])]
    public function getEquivalent(Request $request): JsonResponse
    {
        try {
            $gCo2 = $request->query->get('gCO2');
            $count = $request->query->get('count', 1);

            if (!$gCo2 || !is_numeric($gCo2) || $gCo2 <= 0) {
                return new JsonResponse(['error' => 'Invalid or missing gCO2 parameter'], 400);
            }

            if (!is_numeric($count) || $count <= 0 || $count > 10) {
                return new JsonResponse(['error' => 'Invalid count parameter'], 400);
            }

            $equivalents = $this->equivalentCalculatorService->calculateEquivalents($gCo2, (int)$count);
            return new JsonResponse($equivalents);
            
        } catch (Exception $e) {
            $this->logger->error('Erreur API equivalent : ' . $e->getMessage());
            return new JsonResponse(['error' => 'An error occurred'], 500);
        }
    }
}
