<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MyAccountFormType;

use App\Form\MyOrganisationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MyAccountController extends AbstractController
{

    #[IsGranted('ROLE_USER')]
    #[Route('/mon-compte', name: 'app_my_account')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $user = $this->getUser();

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

    #[IsGranted('ROLE_USER')]
    #[Route('/mon-compte/organisation', name: 'app_my_account_show_organisation')]
    public function myOrganisation(
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response
    {
        $user = $this->getUser();

        return $this->render('my_account/my_organisation_user_side.html.twig', [
            'user' => $user,
            'showMyOrga' => true,
            'organisation' => $user?->getOrganisation() ?? null,
        ]);
    }

    #[IsGranted('ROLE_ORGANISATION')]
    #[Route('/mon-organisation', name: 'app_my_organisation')]
    public function myOrganisationAccount(
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $user = $this->getUser();
        $organisation = $user->getOrganisation();

        $form = $this->createForm(MyOrganisationFormType::class, $organisation);
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

            return $this->redirectToRoute('app_my_organisation');
        }

        return $this->render('my_organisation/my_account_organisation_side.html.twig', [
            'MyAccountForm' => $form,
            'user' => $user,
            'organisation' => $organisation,
            'showMyOrga' => false,
        ]);
    }

    #[IsGranted('ROLE_ORGANISATION')]
    #[Route('/mon-compte/organisation', name: 'app_handle_organisation')]
    public function handleOrganisation(
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response
    {
        $user = $this->getUser();

        return $this->render('my_organisation/my_organisation_user_side.html.twig', [
            'user' => $user,
            'showMyOrga' => true,
            'organisation' => $user?->getOrganisation() ?? null,
        ]);
    }




}
