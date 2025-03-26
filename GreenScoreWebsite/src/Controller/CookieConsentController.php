<?php
namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/*!
 * Cette classe est un controller qui permet de conserver le cookie de consentement à l'utilisateur
 */
class CookieConsentController extends AbstractController
{
    #[Route('/cookie/consent', name: 'cookie_consent', methods: ['POST'])]
    public function saveCookieConsent(Request $request): JsonResponse
    {
        $response = new JsonResponse(['status' => 'success']);
        
        $cookie = Cookie::create('cookie_consent')
            ->withValue('accepted')
            ->withExpires((new DateTime())->modify('+1 year'))
            ->withPath('/')
            ->withSecure(true)
            ->withHttpOnly(true)
            ->withSameSite('lax');
        
        $response->headers->setCookie($cookie);
        
        return $response;
    }
}