<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MyAccountFormType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class MyAccountController extends AbstractController
{
    #[Route('/mon-compte', name: 'app_my_account')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Redirigez l'utilisateur vers la page de login
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(MyAccountFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('plainPassword')->getData() !== null) {
                /** @var string $plainPassword */
                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash("success", "La modification de votre compte a bien été prise en compte.");

            return $this->redirectToRoute('app_my_account');
        }

        return $this->render('my_account/my_account_user_side.html.twig', [
            'MyAccountForm' => $form,
            'user' => $user,
            'showMyOrga' => false,
        ]);
    }

    #[Route('/mon-compte/organisation', name: 'app_my_account_show_organisation')]
    public function myOrganisation(
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Redirigez l'utilisateur vers la page de login
            return $this->redirectToRoute('login');
        }

        return $this->render('my_account/my_organisation_user_side.html.twig', [
            'user' => $user,
            'showMyOrga' => true,
            'organisation' => $user?->getOrganisation() ?? null,
        ]);
    }


}
