<?php

namespace App\Tests\Controller;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationLoginTest extends WebTestCase
{
    public function testSuccessfulRegistrationUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');

        // Vérifier que la page s'affiche correctement
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Prêts à agir ?');

        // Remplir et soumettre le formulaire
        $form = $crawler->selectButton('Inscription')->form([
            'registration_form[firstName]' => 'test',
            'registration_form[lastName]' => 'test',
            'registration_form[email]' => 'test@example.com',
            'registration_form[plainPassword]' => 'MotDePasse123!',
            'registration_form[passwordConfirmation]' => 'MotDePasse123!',
            'registration_form[agreeTerms]' => true,
        ]);

        $client->submit($form);

        // Vérifier que l'utilisateur existe en base de données
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'test@example.com']);
        $this->assertNotNull($user);
    }

    public function testSuccessfulRegistrationOrga(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription-organisation');

        // Vérifier que la page s'affiche correctement
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Observez l’empreinte carbonne de votre organisation sur le web !');

        // Remplir et soumettre le formulaire
        $form = $crawler->selectButton('Inscription')->form([
            'registration_organisation_form[organisationName]' => 'test',
            'registration_organisation_form[email]' => 'test@orga.com',
            'registration_organisation_form[plainPassword]' => 'MotDePasse123!',
            'registration_organisation_form[passwordConfirmation]' => 'MotDePasse123!',
            'registration_organisation_form[agreeTerms]' => true,
        ]);

        $client->submit($form);

        // Vérifier que l'utilisateur existe en base de données
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'test@orga.com']);
        $this->assertNotNull($user);
    }

    /**
     * @depends testSuccessfulRegistrationUser
     */
    public function testInvalidRegistration(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');

        // Soumettre un formulaire avec un email invalide
        $form = $crawler->selectButton('Inscription')->form([
            'registration_form[firstName]' => 'test',
            'registration_form[lastName]' => 'test',
            'registration_form[email]' => 'invalid-email',
            'registration_form[plainPassword]' => 'short',
            'registration_form[passwordConfirmation]' => 'short',
            'registration_form[agreeTerms]' => false,
        ]);

        $client->submit($form);
        $this->assertSelectorTextContains('div', 'Veuillez entrer une adresse e-mail valide.');
        $this->assertSelectorTextContains('div', 'Votre mot de passe de avoir au moins 6 caractères.');
    }

    public function testLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Vérifier que la page s'affiche correctement
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Vous nous avez manqué !');

        // Remplir et soumettre le formulaire
        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'test@example.com',
            'password' => 'MotDePasse123!',
        ]);

        $client->submit($form);

        // Vérifier la redirection vers la page d'accueil ou le dashboard
        $this->assertResponseRedirects();
        $client->followRedirect();
    }
}