<?php

namespace App\Controller;

use App\Repository\MonitoredWebsiteRepository;
use App\Repository\AdviceRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/*!
 * Cette classe est le controller qui permet de récupérer les données de la dernière page Web consultée
 * LPC : Last Page Consulted
 */
class LPCDashboardController extends BaseDashboardController
{
    #[Route('/derniere-page-web-consultee', name: 'app_last_page_consulted')]
    public function monitoredWebsite(
        ParameterBagInterface $params,
        Request $request,
        ?int $userId,
        MonitoredWebsiteRepository $monitoredWebsiteRepository,
        AdviceRepository $adviceRepository
    ): Response {

        $noData = false;
        $user = $this->getUser();

        $data = $this->retrieveWebsiteData($user, $request, $monitoredWebsiteRepository);

        if (!$data) {
            $noData = true;
        }

        $carbonIntensity = null;
        $flagUrl = null;
        $error = null;

        if (!$noData) {
            try {
                // Récupération du drapeau et du code pays
                [$flagUrl, $countryCode] = $this->getCountryData($data['country']);
                
                // Récupération de l'intensité carbone
                $carbonIntensity = $this->getCarbonIntensity($countryCode);

            } catch (Exception $e) {
                $error = $e->getMessage();
            }

            // Conseils
            $advice = $adviceRepository->findRandomByIsDev(false)?->getAdvice();
            $adviceDev = $adviceRepository->findRandomByIsDev(true)?->getAdvice();

            // Équivalents
            $equivalent1 = $equivalent2 = null;
            try {
                $equivalents = $this->equivalentCalculatorService->calculateEquivalents($data['totalConsu'], 2);
                if (count($equivalents) >= 2) {
                    [$equivalent1, $equivalent2] = $equivalents;
                }
            } catch (Exception $e) {
                $this->logger->error('Erreur lors du calcul des équivalents : ' . $e->getMessage());
            }

            // Badge GreenScore
            $envNomination = $letterGreenScore = null;
            try {
                $greenScore = $this->calculateGreenScoreService->calculateGreenScore($data['totalConsu'], 'derniere-page-consultee');
                if ($greenScore) {
                    $envNomination = $greenScore[0]['envNomination'] ?? null;
                    $letterGreenScore = $greenScore[0]['letterGreenScore'] ?? null;
                }
            } catch (Exception $e) {
                $this->logger->error('Erreur lors de la récupération du GreenScore : ' . $e->getMessage());
            }
        }

        if (!$noData) {
            return $this->render('dashboards/last_page_consulted.html.twig', [
                'page' => 'derniere-page-web-consultee',
                'title' => 'Dernière page web consultée',
                'link' => $data['url_full'] ?? null,
                'description' => 'Voici une analyse détaillée de votre dernière page consultée : ',
                'country' => $data['country'] ?? null,
                'flagUrl' => $flagUrl ?? null,
                'error' => $error ?? null,
                'totalConsu' => $this->formatConsumption($data['totalConsu'] ?? null),
                'totalConsuUnit' => $this->formatUnitConsumption($data['totalConsu'] ?? null),
                'advice' => $advice ?? null,
                'adviceDev' => $adviceDev ?? null,
                'equivalent1' => $equivalent1 ?? null,
                'equivalent2' => $equivalent2 ?? null,
                'carbonIntensity' => $carbonIntensity ?? null,
                'pageSize' => $this->formatSize($data['pageSize'] ?? null),
                'pageSizeUnit' => $this->formatUnitSize($data['pageSize'] ?? null),
                'loadingTime' => round($data['loadingTime'] ?? 0, 1),
                'queriesQuantity' => $data['queriesQuantity'] ?? null,
                'url_full' => $data['url_full'] ?? null,
                'noDatas' => $noData,
                'letterGreenScore' => $letterGreenScore ?? null,
                'envNomination' => $envNomination ?? null,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    // Fonction pour récupérer les données du site
    private function retrieveWebsiteData($user, Request $request, MonitoredWebsiteRepository $repository): ?array
    {
        if ($user) {
            $lastWebsite = $repository->findLastAddedByUser($user->getId());
            if ($lastWebsite) {
                return [
                    'url_full' => $lastWebsite->getUrlFull(),
                    'country' => $lastWebsite->getCountry(),
                    'pageSize' => $lastWebsite->getResources(),
                    'loadingTime' => $lastWebsite->getLoadingTime(),
                    'queriesQuantity' => $lastWebsite->getQueriesQuantity(),
                    'totalConsu' => $lastWebsite->getCarbonFootprint(),
                ];
            }
        } else if ($request->query->has('url_full')) {
            return [
                'url_full' => $request->get('url_full'),
                'country' => $request->get('country'),
                'pageSize' => (float)$request->get('pageSize'),
                'loadingTime' => (float)$request->get('loadingTime'),
                'queriesQuantity' => (int)$request->get('queriesQuantity'),
                'totalConsu' => (float)$request->get('totalConsu'),
            ];
        }
        return null;
    }

    // Fonction pour récupérer le drapeau et le code pays
    private function getCountryData(string $country): array
    {
        $response = $this->httpClient->request('GET', 'https://restcountries.com/v3.1/name/' . strtolower($country));
        $data = $response->toArray();
        return [
            $data[0]['flags']['svg'] ?? null,
            $data[0]['cca2'] ?? null,
        ];
    }

    // Fonction pour récupérer l'intensité carbone
    private function getCarbonIntensity(string $countryCode): ?float
    {
        $apiToken = $_ENV['API_ELECTRICITY_MAP_KEY'];
        $response = $this->httpClient->request('GET', 'https://api.electricitymap.org/v3/carbon-intensity/latest?', [
            'headers' => [
                'auth-token' => $apiToken,
            ],
            'query' => [
                'zone' => $countryCode,
            ],
        ]);
        $data = $response->toArray();
        return $data['carbonIntensity'] ?? null;
    }
}
