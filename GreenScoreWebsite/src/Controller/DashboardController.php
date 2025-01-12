<?php

namespace App\Controller;

use App\Repository\MonitoredWebsiteRepository;
use App\Repository\UserRepository;
use App\Repository\AdviceRepository;
use App\Repository\EquivalentRepository;
use App\Service\EquivalentCalculatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

class DashboardController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private EquivalentCalculatorService $equivalentCalculatorService;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, EquivalentCalculatorService $equivalentCalculatorService)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->equivalentCalculatorService = $equivalentCalculatorService;
    }

    #[Route('/mon-organisation', name: 'app_mon_organisation')]
    public function monOrganisation(EntityManagerInterface $entityManager, MonitoredWebsiteRepository $monitoredWebsiteRepository): Response
    {
    //     S6 : dashboard donnees utilisateur et organisation

    //     $advice = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt.";
    //     $adviceDev = "Activez le mode sombre pour réduire l'énergie consommée par votre écran.";
    //     $totalConsu = 65000;
    //     $averageFootprint = 320;
    //     $equivalent1 = 20;
    //     $equivalent2 = 20;
    //     $equivalentAverage = 20;
    //     $top5Sites = [
    //         ["Youtube", 800],
    //         ["Facebook", 750],
    //         ["Netflix", 700],
    //         ["Instagram", 650],
    //         ["Tik Tok", 600]
    //     ];

        return $this->render('dashboards/index.html.twig', [
            'page' => 'mon-organisation',
            'title' => 'Mon Organisation',
            // 'description' => 'bla bla bla',
            // 'totalConsu' => $totalConsu,
            // 'advice' => $advice,
            // 'adviceDev' => $adviceDev,
            // 'averageFootprint' => $averageFootprint,
            // 'equivalent1' => $equivalent1,
            // 'equivalent2' => $equivalent2,
            // 'equivalentAverage' => $equivalentAverage,
            // 'top5Sites' => $top5Sites,
            'noDatas' => true,
        ]);
    }

    #[Route('/mes-donnees', name: 'app_mes_donnees')]
    public function mesDonnees(): Response
    {
        
    //     S6 : dashboard donnees utilisateur et organisation

    //     $advice = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt.";
    //     $adviceDev = "Activez le mode sombre pour réduire l'énergie consommée par votre écran.";
    //     $totalConsu = 65000;
    //     $myAverageFootprint = 320;
    //     $messageAverageFootprint = "Bravo ! Votre empreinte carbone est plus basse que la moyenne !!";
    //     $averageFootprint = 320;
    //     $equivalent1 = 20;
    //     $equivalent2 = 20;
    //     $equivalent2 = 20;
    //     $equivalent2 = 20;
    //     $top5Sites = [
    //         ["Youtube", 800],
    //         ["Facebook", 750],
    //         ["Netflix", 700],
    //         ["Instagram", 650],
    //         ["Tik Tok", 600]
    //     ];

        return $this->render('dashboards/index.html.twig', [
            'page' => 'mes-donnees',
            'title' => 'Mes Données',
            // 'description' => 'bla bla bla',
            // 'totalConsu' => $totalConsu,
            // 'advice' => $advice,
            // 'adviceDev' => $adviceDev,
            // 'averageFootprint' => $averageFootprint,
            // 'equivalent1' => $equivalent1,
            // 'equivalent2' => $equivalent2,
            // 'myAverageFootprint' => $myAverageFootprint,
            // 'messageAverageFootprint' => $messageAverageFootprint,
            // 'top5Sites' => $top5Sites,
            'noDatas' => true,
        ]);
    }

    #[Route('/derniere-page-web-consultee', name: 'app_derniere_page_web_consultee')]
    public function siteWebSurveille(Request $request, ?int $userId, MonitoredWebsiteRepository $monitoredWebsiteRepository, UserRepository $userRepository, AdviceRepository $adviceRepository, EquivalentRepository $equivalentRepository): Response
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
                try {
                    $user = $userRepository->find($userId);
            
                    if (!$user) {
                        throw new Exception('Utilisateur non trouvé');
                    }

                    $totalConsu = $user->getTotalCarbonFootprint();
                } catch (Exception $e) {
                    $totalConsu = null;
                }
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
                $apiToken = 'R1oPkUx8UdRnx';
                try {
                    $response = $this->httpClient->request('GET', 'https://api.electricitymap.org/v3/carbon-intensity/latest', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $apiToken,
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

            // Equivalents : Recuperer deux equivalents aleatoires
            if ($totalConsu) {
                try {
                    $equivalents = $this->equivalentCalculatorService->calculateEquivalents($totalConsu, 2);
                    if (count($equivalents) >= 2) {
                        $equivalent1 = $equivalents[0];
                        $equivalent2 = $equivalents[1];
                    }
                } catch (Exception $e) {
                    $this->logger->error('Erreur lors du calcul des équivalents : ' . $e->getMessage());
                }
            }

            // Widget en fonction de l'eco index
            try {
                $urlSvgEcoIndex = 'https://bff.ecoindex.fr/badge/?theme=dark&url=' . $url_full;
                $svgContent = file_get_contents($urlSvgEcoIndex);

                if (preg_match('/<tspan[^>]*>([A-G])<\/tspan>/', $svgContent, $matches)) {
                    $letterEcoIndex = $matches[1];
                }
                
                switch ($letterEcoIndex) {
                    case 'A':
                        $envNomination = 'Maître des Forêts';
                        break;
                    case 'B':
                        $envNomination = 'Protecteur des Bois';
                        break;
                    case 'C':
                        $envNomination = 'Frère des Arbres';
                        break;
                    case 'D':
                        $envNomination = 'Initié de la Nature';
                        break;
                    case 'E':
                        $envNomination = 'Explorateur Imprudent';
                        break;
                    case 'F':
                        $envNomination = 'Tempête Numérique';
                        break;
                    case 'G':
                        $envNomination = 'Bouleverseur des Écosystèmes';
                        break;
                    default:
                        $envNomination = null;
                        break;
                }
            } catch (Exception $e) {
                $this->logger->error('Erreur lors de la récupération du badge EcoIndex : ' . $e->getMessage());
            }

        }
        
        if($showDatas || $userId)
            return $this->render('dashboards/index.html.twig', [
                'page' => 'derniere-page-web-consultee',
                'title' => 'Dernière page web consultée',
                'link' => $url_full ?? null,
                'description' => 'Voici une analyse détaillée de votre dernière page consultée : ',
                'country' => $country ?? null,
                'flagUrl' => $flagUrl ?? null,
                'error' => $error ?? null,
                'totalConsu' => $totalConsu ?? null,
                'advice' => $advice ?? null,
                'adviceDev' => $adviceDev ?? null,
                'equivalent1' => $equivalent1 ?? null,
                'equivalent2' => $equivalent2 ?? null,
                'carbonIntensity' => $carbonIntensity ?? null,
                'pageSize' => $pageSize ?? null,
                'loadingTime' => round($loadingTime, 1) ?? null,
                'queriesQuantity' => $queriesQuantity ?? null,
                'url_full' => $url_full ?? null,
                'noDatas' => $noDatas ?? null,
                'letterEcoIndex' => $letterEcoIndex ?? null,
                'envNomination' => $envNomination ?? null,
            ]);
        else
            return $this->redirectToRoute('app_login');
    }

    // #[Route('/api/top-sites', name: 'api_top_sites')]
    // public function getTopSites(): Response
    // {
    //     $top5Sites = [
    //         ["Youtube", 800],
    //         ["Facebook", 750],
    //         ["Netflix", 700],
    //         ["Instagram", 650],
    //         ["Tik Tok", 600]
    //     ];

    //     return $this->json($top5Sites);
    // }

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
