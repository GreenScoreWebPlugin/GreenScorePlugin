<?php

namespace App\Controller;

use App\Repository\MonitoredWebsiteRepository;
use App\Repository\UserRepository;
use App\Repository\AdviceRepository;
use App\Repository\EquivalentRepository;
use App\Service\EquivalentCalculatorService;
use Doctrine\ORM\EntityManagerInterface;
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
        $advice = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt.";
        $adviceDev = "Activez le mode sombre pour réduire l'énergie consommée par votre écran.";
        $totalConsu = 65000;
        $averageFootprint = 320;
        $equivalent1 = 20;
        $equivalent2 = 20;
        $equivalentAverage = 20;
        $top5Sites = [
            ["Youtube", 800],
            ["Facebook", 750],
            ["Netflix", 700],
            ["Instagram", 650],
            ["Tik Tok", 600]
        ];

        // Création d'une instance d'une entité
        // $website = new MonitoredWebsite(); 
        // $website->setUrlDomain('luciedubos.fr');
        
        // Avec persist, on dit qu'elle sera sauvegardée en BD
        // $entityManager->persist($website);

        // Avec flush, on sauvegarde les modifications en cours sur toutes les entités persistées
        // $entityManager->flush();

        // Avec le Repository, on recupère toutes les instances de l'entité stockées en base
        // $monitoredwebsites = $monitoredWebsiteRepository->findAll()); 

        // On récupère tous ceux dont user est égal au user $user et dont l'urlDomain est luceidubos.fr, triés par urlFull décroissant
        // $monitoredwebsites = $monitoredWebsiteRepository->findBy(['user' => $user, 'urlDomain' => 'luciedubos.fr'], ['urlFull' => 'desc']); 

        // Find est l'équivalent de WHERE et ORDER BY

        return $this->render('dashboards/index.html.twig', [
            'page' => 'mon-organisation',
            'title' => 'Mon Organisation',
            'description' => 'bla bla bla',
            'totalConsu' => $totalConsu,
            'advice' => $advice,
            'adviceDev' => $adviceDev,
            'averageFootprint' => $averageFootprint,
            'equivalent1' => $equivalent1,
            'equivalent2' => $equivalent2,
            'equivalentAverage' => $equivalentAverage,
            'top5Sites' => $top5Sites,
        ]);
    }

    #[Route('/mes-donnees', name: 'app_mes_donnees')]
    public function mesDonnees(): Response
    {
        
        $advice = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt.";
        $adviceDev = "Activez le mode sombre pour réduire l'énergie consommée par votre écran.";
        $totalConsu = 65000;
        $myAverageFootprint = 320;
        $messageAverageFootprint = "Bravo ! Votre empreinte carbone est plus basse que la moyenne !!";
        $averageFootprint = 320;
        $equivalent1 = 20;
        $equivalent2 = 20;
        $equivalent2 = 20;
        $equivalent2 = 20;
        $top5Sites = [
            ["Youtube", 800],
            ["Facebook", 750],
            ["Netflix", 700],
            ["Instagram", 650],
            ["Tik Tok", 600]
        ];

        return $this->render('dashboards/index.html.twig', [
            'page' => 'mes-donnees',
            'title' => 'Mes Données',
            'description' => 'bla bla bla',
            'totalConsu' => $totalConsu,
            'advice' => $advice,
            'adviceDev' => $adviceDev,
            'averageFootprint' => $averageFootprint,
            'equivalent1' => $equivalent1,
            'equivalent2' => $equivalent2,
            'myAverageFootprint' => $myAverageFootprint,
            'messageAverageFootprint' => $messageAverageFootprint,
            'top5Sites' => $top5Sites,
        ]);
    }

    #[Route('/dernier-site-web-consulte', name: 'app_dernier_site_web_consulte', defaults: ['userId' => 1])]
    public function siteWebSurveille(?int $userId, MonitoredWebsiteRepository $monitoredWebsiteRepository, UserRepository $userRepository, AdviceRepository $adviceRepository, EquivalentRepository $equivalentRepository): Response
    {
        // $user = $this->getUser();

        // Recuperation du dernier site web consulte
        $lastMonitoredWebsite = $monitoredWebsiteRepository->findLastAddedByUser($userId);

        // Variables affichees
        $flagUrl = null;
        $country = null;
        $url_domain = null;
        $url_full = null;
        $error = null;
        $carbonIntensity = null;
        $equivalent1 = null;
        $equivalent2 = null;
        $advice = null;
        $adviceDev = null;
        $totalConsu = null;
        $pageSize = null;
        $loadingTime = null;
        $queriesQuantity = null;

        if ($lastMonitoredWebsite) {
            // Website
            $url_full = $lastMonitoredWebsite->getUrlFull();
            $url_domain = $lastMonitoredWebsite->getUrlDomain();

            // Country Flag Carbon Intensity
            $country = $lastMonitoredWebsite->getCountry();
            // TODO : Recuperer l'intensite carbone du pays
            $carbonIntensity = 64;
            try {
                $response = $this->httpClient->request('GET', 'https://restcountries.com/v3.1/name/' . strtolower($country));
                $data = $response->toArray();
                $flagUrl = $data[0]['flags']['svg'] ?? null;
            } catch (\Exception $e) {
                $error = 'Impossible de récupérer les informations pour ce pays.';
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

            // Total Consumption
            try {
                $user = $userRepository->find($userId);
        
                if (!$user) {
                    throw new \Exception('Utilisateur non trouvé');
                }

                $totalConsu = $user->getTotalCarbonFootprint();
            } catch (\Exception $e) {
                $totalConsu = null;
            }

            // Equivalents : Recuperer deux equivalents aleatoires
            if ($totalConsu) {
                try {
                    $equivalents = $this->equivalentCalculatorService->calculateEquivalents($totalConsu, 2);
                    if (count($equivalents) >= 2) {
                        $equivalent1 = $equivalents[0];
                        $equivalent2 = $equivalents[1];
                    }
                } catch (\Exception $e) {
                    $this->logger->error('Erreur lors du calcul des équivalents : ' . $e->getMessage());
                }
            }
            

            // Page in numbers
            $pageSize = $lastMonitoredWebsite->getResources();
            $loadingTime = $lastMonitoredWebsite->getLoadingTime();
            $queriesQuantity = $lastMonitoredWebsite->getQueriesQuantity();           

        }

        return $this->render('dashboards/index.html.twig', [
            'page' => 'dernier-site-web-consulte',
            'title' => 'Dernier site web consulté : ' . $url_domain,
            'description' => 'Voici une analyse détaillée de votre dernière page consultée : ' . $url_full,
            'country' => $country,
            'flagUrl' => $flagUrl,
            'error' => $error,
            'totalConsu' => $totalConsu,
            'advice' => $advice,
            'adviceDev' => $adviceDev,
            'equivalent1' => $equivalent1,
            'equivalent2' => $equivalent2,
            'carbonIntensity' => $carbonIntensity,
            'pageSize' => $pageSize,
            'loadingTime' => $loadingTime,
            'queriesQuantity' => $queriesQuantity,
            'url_full' => $url_full,
        ]);
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
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur API equivalent : ' . $e->getMessage());
            return new JsonResponse(['error' => 'An error occurred'], 500);
        }
    }


    #[Route('/example', name: 'example')]
    public function example(Request $request): Response
    {
        $lulu = $request->get('lulu');
        $toto = $request->get('toto');

        if (isset($lulu, $toto)) {
            print($lulu . ' ' . $toto);
        }

        return new Response();
    }
}
