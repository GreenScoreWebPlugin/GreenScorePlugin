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

    #[Route('/dernier-site-web-consulte', name: 'app_dernier_site_web_consulte')]
    public function siteWebSurveille(): Response
    {
        return $this->render('dashboards/index.html.twig', [
            'page' => 'dernier-site-web-consulte', 
            'title' => 'Dernier site web consulté'
        ]);
    }
}
