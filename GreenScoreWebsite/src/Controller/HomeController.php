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
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_home')]
    public function index(OrganisationRepository $organisationRepository): Response
    {
        $user = new User();
        $user->setEmail('admin@greenscore.com');
        $user->setPassword('admin');
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->render('home/index.html.twig');
    }
}
