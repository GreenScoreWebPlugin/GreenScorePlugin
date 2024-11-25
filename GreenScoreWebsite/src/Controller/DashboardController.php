<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
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

    #[Route('/site-web-surveille', name: 'app_site_web_surveille')]
    public function siteWebSurveille(): Response
    {
        return $this->render('dashboards/index.html.twig', [
            'page' => 'site-web-surveille', 
            'title' => 'Site Web Surveillé'
        ]);
    }
}
