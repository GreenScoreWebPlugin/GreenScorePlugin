<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FooterController extends AbstractController
{
    #[Route('/conditions-generales-d-utilisation', name: 'app_get_conditions_generales_d_utilisation')]
    public function getConditionsGeneralDUtilisation(): Response
    {
        return $this->render('footer/conditions_generales_d_utilisation.html.twig');
    }

    #[Route('/politique-de-confidentialite', name: 'app_get_politique_de_confidentialite')]
    public function getPolitiqueDeConfidentialite(): Response
    {
        return $this->render('footer/politique_de_confidentialite.html.twig');
    }
}
