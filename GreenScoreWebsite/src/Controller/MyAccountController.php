<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Entity\User;
use App\Form\MyAccountFormType;

use App\Form\MyOrganisationFormType;
use App\Repository\OrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MyAccountController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/mon-compte', name: 'app_my_account')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(MyAccountFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('plainPassword')->getData() !== null) {
                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash("success", "La modification de votre compte a bien été prise en compte.");

            // Redirect to prevent form resubmission
            return $this->redirectToRoute('app_my_account');
        }

        return $this->render('my_account/my_account_user_side.html.twig', [
            'MyAccountForm' => $form->createView(),
            'user' => $user,
            'showMyOrga' => false,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/mon-compte/organisation', name: 'app_my_account_show_organisation')]
    public function myOrganisation(
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response {

        $user = $this->getUser();

        return $this->render('my_account/my_organisation_user_side.html.twig', [
            'user' => $user,
            'showMyOrga' => true,
            'organisation' => $user?->getOrganisation() ?? null,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/mon-compte/organisation/remove-organisation', name: 'app_remove_organisation')]
    public function leaveOrganisation(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $user->setOrganisation(null);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez quitté l\'organisation avec succès');

        return $this->redirectToRoute('app_my_account_show_organisation');
    }

    #[Route('/mon-compte/organisation/exist', name: 'app_check_if_organisation_exist')]
    public function checkCodeOrganisationExists(Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        if (!preg_match('/^[0-9A-Z]{8}$/', $data['code'])) {
            return new JsonResponse(['success' => false, 'errorMessage' => 'Code invalide'], 400);
        }

        $code = $data['code'];

        if ($entityManager->getRepository(Organisation::class)->findOneBy(['organisationCode' => $code]) === null) {
            return new JsonResponse(['success' => false, 'errorMessage' => 'Ce code n\'est pas valide'], 400);
        }

        return new JsonResponse(['success' => true]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/mon-compte/organisation/change-organisation', name: 'app_change_or_join_organisation', methods: ['POST'])]
    public function changeOrJoinOrganisation(
        Request $request,
        OrganisationRepository $organisationRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Rechercher l'organisation avec ce code
        $organisation = $organisationRepository->findOneBy(['organisationCode' => $request->get('codeOrganisation')]);

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        $organisation->addUser($user);

        // Sauvegarder en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez bien rejoint l\'organisation nommée ' . $organisation->getOrganisationName());

        return $this->redirectToRoute('app_my_account_show_organisation');
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
