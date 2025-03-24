<?php

namespace App\Controller;

use App\Repository\MonitoredWebsiteRepository;
use App\Repository\AdviceRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LPCDashboardController extends BaseDashboardController
{
    #[Route('/derniere-page-web-consultee', name: 'app_last_page_consulted')]
    public function monitoredWebsite(ParameterBagInterface $params, Request $request, ?int $userId, MonitoredWebsiteRepository $monitoredWebsiteRepository, AdviceRepository $adviceRepository): Response
    {
        // dd($userId);
        // dump($toto);

        $showDatas = false;
        $noDatas = false;
        $user = $this->getUser();

        if ($user){
            $userId = $user->getId();
            $country = null;

            // Recuperation du dernier site web consulte
            $lastMonitoredWebsite = $monitoredWebsiteRepository->findLastAddedByUser($userId);
            if ($lastMonitoredWebsite) {
                // Website
                $url_full = $lastMonitoredWebsite->getUrlFull();

                // Country
                $country = $lastMonitoredWebsite->getCountry();

                // Page in numbers
                $pageSize = $lastMonitoredWebsite->getResources();
                $loadingTime = $lastMonitoredWebsite->getLoadingTime();
                $queriesQuantity = $lastMonitoredWebsite->getQueriesQuantity(); 

                // Total Consumption
                $totalConsu = $lastMonitoredWebsite->getCarbonFootprint(); 
                
                $showDatas = true;
            } else {
                $showDatas = false;
                $noDatas = true;
            }
            
        }
        else if($request->query->has('url_full'))
        {
            // dump($request->get('country'));
            
            $country = is_string($request->get('country')) ? $request->get('country') : null;
            $url_full = is_string($request->get('url_full')) ? $request->get('url_full') : null;
            $totalConsu = is_numeric($request->get('totalConsu')) ? (float)$request->get('totalConsu') : null;
            $pageSize = is_numeric($request->get('pageSize')) ? (float)$request->get('pageSize') : null;
            $loadingTime = is_numeric($request->get('loadingTime')) ? (float)$request->get('loadingTime') : null;
            $queriesQuantity = is_numeric($request->get('queriesQuantity')) ? (int)$request->get('queriesQuantity') : null;

            $showDatas = true;
        }else{
            $showDatas = false;
        }
        
        if ($showDatas) {
            $contryCode = null;

            // Flag Carbon Intensity
            try {
                $response = $this->httpClient->request('GET', 'https://restcountries.com/v3.1/name/' . strtolower($country));
                $data = $response->toArray();
                $flagUrl = $data[0]['flags']['svg'] ?? null;
                $contryCode = $data[0]['cca2'] ?? null;
            } catch (Exception $e) {
                $error = 'Impossible de récupérer les informations pour ce pays.';
            }
            if($contryCode){
                $apiToken = $_ENV['API_ELECTRICITY_MAP_KEY'];
                try {
                    $response = $this->httpClient->request('GET', 'https://api.electricitymap.org/v3/carbon-intensity/latest?', [
                        'headers' => [
                            'auth-token' => $apiToken,
                        ],
                        'query' => [
                            'zone' => $contryCode,
                        ],
                    ]);
                    $data = $response->toArray();
                    $carbonIntensity = $data['carbonIntensity'] ?? null;
                } catch (Exception $e) {
                    $error = 'Impossible de récupérer l intensité carbone pour ce pays.';
                }
            }

            // Advices : Recuperer deux conseils aleatoire
            $adviceEntity = $adviceRepository->findRandomByIsDev(false);
            if ($adviceEntity) {
                $advice = $adviceEntity->getAdvice();
            }
            $adviceDevEntity = $adviceRepository->findRandomByIsDev(true);
            if ($adviceDevEntity) {
                $adviceDev = $adviceDevEntity->getAdvice();
            }

            if ($totalConsu) {
                // Equivalents : Recuperer deux equivalents aleatoires
                try {
                    $equivalents = $this->equivalentCalculatorService->calculateEquivalents($totalConsu, 2);
                    if (count($equivalents) >= 2) {
                        $equivalent1 = $equivalents[0];
                        $equivalent2 = $equivalents[1];
                    }
                } catch (Exception $e) {
                    $this->logger->error('Erreur lors du calcul des équivalents : ' . $e->getMessage());
                }

                // Environmental Impact Badge : Recuperer les donnees du badge GreenScore
                try {
                    $calculateGreenScore = $this->calculateGreenScoreService->calculateGreenScore($totalConsu, 'derniere-page-consultee');
                    if($calculateGreenScore) {
                        $envNomination = $calculateGreenScore[0]['envNomination'];
                        $letterGreenScore = $calculateGreenScore[0]['letterGreenScore'];
                    }

                } catch (Exception $e) {
                    $this->logger->error('Erreur lors de la récupération du GreenScore : ' . $e->getMessage());
                }
            }

        }

        if($showDatas || $userId)
            return $this->render('dashboards/last_page_consulted.html.twig', [
                'page' => 'derniere-page-web-consultee',
                'title' => 'Dernière page web consultée',
                'link' => $url_full ?? null,
                'description' => 'Voici une analyse détaillée de votre dernière page consultée : ',
                'country' => $country ?? null,
                'flagUrl' => $flagUrl ?? null,
                'error' => $error ?? null,
                'totalConsu' => $this->formatConsumption($totalConsu ?? null) ?? null,
                'totalConsuUnit' => $this->formatUnitConsumption($totalConsu ?? null) ?? null,
                'advice' => $advice ?? null,
                'adviceDev' => $adviceDev ?? null,
                'equivalent1' => $equivalent1 ?? null,
                'equivalent2' => $equivalent2 ?? null,
                'carbonIntensity' => $carbonIntensity ?? null,
                'pageSize' => $this->formatSize($pageSize ?? null) ?? null,
                'pageSizeUnit' => $this->formatUnitSize($pageSize ?? null) ?? null,
                'loadingTime' => round($loadingTime ?? null, 1) ?? null,
                'queriesQuantity' => $queriesQuantity ?? null,
                'url_full' => $url_full ?? null,
                'noDatas' => $noDatas ?? null,
                'letterGreenScore' => $letterGreenScore ?? null,
                'envNomination' => $envNomination ?? null,
            ]);
        else
            return $this->redirectToRoute('app_login');
    }
}
