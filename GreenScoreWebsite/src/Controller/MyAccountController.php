<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Entity\User;
use App\Form\MyAccountFormType;

use App\Form\MyOrganisationFormType;
use App\Repository\OrganisationRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/*!
 * Cette classe contient toutes les routes relatives à la gestion du compte de l'utilisateur.
 */
class MyAccountController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/mon-compte', name: 'app_my_account')]
    public function index(
        EntityManagerInterface      $entityManager,
        Request                     $request,
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
        Request                $request,
    ): Response
    {
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
    #[Route('/mon-compte/organisation/change-or-join-organisation', name: 'app_change_or_join_organisation', methods: ['POST'])]
    public function changeOrJoinOrganisation(
        Request $request,
        OrganisationRepository $organisationRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Rechercher l'organisation avec le code
        $organisation = $organisationRepository->findOneBy(['organisationCode' => $request->get('codeOrganisation')]);

        $user = $this->getUser();

        $organisation->addUser($user);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez bien rejoint l\'organisation nommée ' . $organisation->getOrganisationName());

        return $this->redirectToRoute('app_my_account_show_organisation');
    }

    #[Route('/api/user/delete', name: 'api_delete_user', methods: ['DELETE'])]
    public function deleteUser(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        try {
            // Supprimer l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();

            $tokenStorage->setToken(null);
            // Supprimer la session de manière sécurisée
            $session->invalidate();

            return new JsonResponse(['success' => true, 'redirect' => '/inscription']);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'An error occurred'], 500);
        }
    }

    #[IsGranted('ROLE_ORGANISATION')]
    #[Route('/gerer-organisation', name: 'app_handle_organisation')]
    public function myOrganisationAccount(
        EntityManagerInterface      $entityManager,
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $user = $this->getUser();
        $organisation = $user->getIsAdminOf();

        $form = $this->createForm(MyOrganisationFormType::class, $organisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash("success", "La modification de votre compte a bien été prise en compte.");

            return $this->redirectToRoute('app_handle_organisation');
        }

        return $this->render('my_organisation/my_account_organisation_side.html.twig', [
            'MyAccountForm' => $form,
            'user' => $user,
            'organisation' => $organisation,
            'showMyOrga' => false,
        ]);
    }

    #[Route('/gerer-organisation/membres', name: 'app_handle_organisation_members')]
    public function handleOrganisationMembers(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Request $request
    ): Response {
        $user = $this->getUser();
        $organisation = $user->getIsAdminOf();

        $limit = 5;
        $page = max(1, $request->query->getInt('page', 1));
        $searchTerm = $request->query->get('search');

        $members = $userRepository->searchUsers($page, $limit, $organisation->getId(), $searchTerm);
        $totalMembers = $members->count();
        $maxPage = max(1, ceil($totalMembers / $limit));

        // Si la page demandée est supérieure au nombre de pages maximum
        if ($page > $maxPage && $totalMembers > 0) {
            return $this->redirectToRoute('app_handle_organisation_members', [
                'page' => 1,
                'search' => $searchTerm
            ]);
        }

        return $this->render('my_organisation/my_organisation_handle_members.html.twig', [
            'user' => $user,
            'showMyOrga' => true,
            'members' => $members,
            'maxPage' => $maxPage,
            'page' => $page,
            'searchTerm' => $searchTerm,
            'organisation' => $organisation,
        ]);
    }

    #[Route('/gerer-organisation/membres/{id}/delete', name: 'app_delete_organisation_member', methods: ['DELETE'])]
    public function removeMember(
        int $id,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): JsonResponse {
        // Récupérer l'admin et son organisation
        $admin = $this->getUser();
        $organisation = $admin->getIsAdminOf();

        if (!$organisation) {
            return new JsonResponse(['error' => 'Organisation non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Trouver l'utilisateur à retirer
        $member = $userRepository->findOneBy([
            'id' => $id,
            'organisation' => $organisation
        ]);

        if (!$member) {
            return new JsonResponse(['error' => 'Membre non trouvé'], Response::HTTP_NOT_FOUND);
        }

        try {
            // Utiliser la méthode removeUser de l'organisation qui gère la relation bidirectionnelle
            $organisation->removeUser($member);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        } catch (RuntimeException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/organization/delete', name: 'api_delete_organization', methods: ['DELETE'])]
    public function delete(
        EntityManagerInterface $entityManager,
        OrganisationRepository $organisationRepository,
        UserRepository $userRepository,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $organisation = $user->getIsAdminOf();

        if (!$organisation) {
            return new JsonResponse(['error' => 'Organization not found'], 404);
        }

        try {
            // Récupérer tous les membres
            $members = $userRepository->findBy(['organisation' => $organisation]);

            // Supprimer l'association avec l'organisation pour chaque membre
            foreach ($members as $member) {
                $member->setOrganisation(null);
                $entityManager->persist($member);
            }

            // Supprimer l'organisation
            $entityManager->remove($organisation);
            $user->setIsAdminOf(null);

            // Supprimer l'utilisateur
            $entityManager->remove($user);

            $entityManager->flush();

            // Return response with redirection URL
            return new JsonResponse(['success' => true, 'redirect' => '/inscription-organisation']);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'An error occurred'], 500);
        }
    }
}