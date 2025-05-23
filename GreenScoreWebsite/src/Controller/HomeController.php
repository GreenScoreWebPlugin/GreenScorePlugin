<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AdviceRepository;
use App\Repository\OrganisationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/*!
 * Cette classe est un controller qui permet de récupérer la page d'accueil de notre site Web
 */
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(AdviceRepository $adviceRepository, OrganisationRepository $organisationRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $advice = $adviceRepository->getAllAdvice();
        
        return $this->render('home/index.html.twig', [
            'advice' => $advice ?? null
        ]);
    }
}
