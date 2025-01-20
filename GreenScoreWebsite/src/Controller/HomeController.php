<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrganisationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(OrganisationRepository $organisationRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->render('home/index.html.twig');
    }
}
