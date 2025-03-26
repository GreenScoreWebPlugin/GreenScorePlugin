<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/*!
 * Cette classe est le composant sécurité de symfony, il permet de gérer la connexion/déconnexion de l'utilisateur
 */
class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/api/user-info', name: 'api_user_info', methods: ['GET'])] // Il faudra revoir où mettre cette requête API.
    public function getUserInfo(): JsonResponse
    {
        // Vérifie si l'utilisateur est connecté
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(null, JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Retourne les informations de l'utilisateur
        return new JsonResponse([
            'id' => $user->getId(),
        ]);
    }

}
