<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CookieConsentController extends AbstractController
{
    #[Route('/cookie/consent', name: 'cookie_consent', methods: ['POST'])]
    public function saveCookieConsent(Request $request): JsonResponse
    {
        $response = new JsonResponse(['status' => 'success']);
        
        $cookie = Cookie::create('cookie_consent')
            ->withValue('accepted')
            ->withExpires((new \DateTime())->modify('+1 year'))
            ->withPath('/')
            ->withSecure(true)
            ->withHttpOnly(true)
            ->withSameSite('lax');
        
        $response->headers->setCookie($cookie);
        
        return $response;
    }
}