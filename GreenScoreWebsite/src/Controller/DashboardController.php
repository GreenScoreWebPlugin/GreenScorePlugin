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
        return $this->render('dashboards/index.html.twig', [
            'page' => 'mon-organisation',
            'title' => 'Mon Organisation'
        ]);
    }

    #[Route('/mes-donnees', name: 'app_mes_donnees')]
    public function mesDonnees(): Response
    {
        return $this->render('dashboards/index.html.twig', [
            'page' => 'mes-donnees',
            'title' => 'Mes Données'
        ]);
    }

    #[Route('/dernier-site-web-consulte', name: 'app_dernier_site_web_consulte')]
    public function siteWebSurveille(): Response
    {
        $countryName = 'France'; // Le pays que vous voulez afficher
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

        return $this->render('dashboards/index.html.twig', [
            'page' => 'dernier-site-web-consulte',
            'title' => 'Dernier site web consulté',
            'country' => $countryName,
            'flagUrl' => $flagUrl,
            'error' => $error,
        ]);
    }
}
