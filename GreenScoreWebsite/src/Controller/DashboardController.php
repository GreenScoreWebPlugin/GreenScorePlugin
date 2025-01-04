<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DashboardController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/mon-organisation', name: 'app_mon_organisation')]
    public function monOrganisation(): Response
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

    #[Route('/dernier-site-web-consulte', name: 'app_dernier_site_web_consulte')]
    public function siteWebSurveille(): Response
    {
        $countryName = 'Japan'; // Le pays que vous voulez afficher
        $flagUrl = null;
        $error = null;

        try {
            $response = $this->httpClient->request('GET', 'https://restcountries.com/v3.1/name/' . strtolower($countryName));
            $data = $response->toArray();

            // Récupère le lien du drapeau
            $flagUrl = $data[0]['flags']['svg'] ?? null;
        } catch (\Exception $e) {
            $error = 'Impossible de récupérer les informations pour ce pays.';
        }

        $advice = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt.";
        $adviceDev = "Activez le mode sombre pour réduire l'énergie consommée par votre écran.";
        $totalConsu = 65000;
        $equivalent1 = 20;
        $equivalent2 = 20;
        $carbonIntensity = 64;
        $pageSize = 230;
        $loadingTime = 230;
        $queriesQuantity = 230;
        $url = "https://example.com";

        return $this->render('dashboards/index.html.twig', [
            'page' => 'dernier-site-web-consulte',
            'title' => 'Dernier site web consulté',
            'description' => 'bla bla bla',
            'country' => $countryName,
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
            'url' => $url,
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
