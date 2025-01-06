<?php

namespace App\Controller;

use App\Entity\MonitoredWebsite;
use App\Repository\MonitoredWebsiteRepository;
use App\Repository\UserRepository;
use App\Repository\AdviceRepository;
use App\Repository\EquivalentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DashboardController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
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

            if ($totalConsu) {
                // Equivalents : Recuperer deux equivalents aleatoires
                $equivalents = $equivalentRepository->findTwoRandomEquivalents();

                $equivalent1 = null;
                $equivalent2 = null;

                if (count($equivalents) === 2) {
                    $equivalent1 = [
                        'name' => $equivalents[0]->getName(),
                        'value' => $totalConsu / $equivalents[0]->getCarbonKgCo2(),
                        'icon' => $equivalents[0]->getIconThumbnail(),
                    ];
                    $equivalent2 = [
                        'name' => $equivalents[1]->getName(),
                        'value' => $totalConsu / $equivalents[1]->getCarbonKgCo2(),
                        'icon' => $equivalents[1]->getIconThumbnail(),
                    ];
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

    #[Route('/api/top-sites', name: 'api_top_sites')]
    public function getTopSites(): Response
    {
        $top5Sites = [
            ["Youtube", 800],
            ["Facebook", 750],
            ["Netflix", 700],
            ["Instagram", 650],
            ["Tik Tok", 600]
        ];

        return $this->json($top5Sites);
    }
}
