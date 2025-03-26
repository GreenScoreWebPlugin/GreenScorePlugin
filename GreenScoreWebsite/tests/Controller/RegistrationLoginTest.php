<?php

namespace App\Tests\Controller;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationLoginTest extends WebTestCase
{
    public function testSuccessfulRegistrationUser(): void
    {
        // GIVEN
        $client = static::createClient();

        // WHEN
        $crawler = $client->request('GET', '/inscription');

        // THEN
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Prêts à agir ?');

        // GIVEN
        $form = $crawler->selectButton('Inscription')->form([
            'registration_form[firstName]' => 'test',
            'registration_form[lastName]' => 'test',
            'registration_form[email]' => 'test@example.com',
            'registration_form[plainPassword]' => 'MotDePasse123!',
            'registration_form[passwordConfirmation]' => 'MotDePasse123!',
            'registration_form[agreeTerms]' => true,
        ]);

        // WHEN
        $client->submit($form);

        // THEN
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'test@example.com']);
        $this->assertNotNull($user);
    }

    public function testSuccessfulRegistrationOrga(): void
    {
        // GIVEN
        $client = static::createClient();

        // WHEN
        $crawler = $client->request('GET', '/inscription-organisation');

        // THEN
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Observez l’empreinte carbonne de votre organisation sur le web !');

        // GIVEN
        $form = $crawler->selectButton('Inscription')->form([
            'registration_organisation_form[organisationName]' => 'test',
            'registration_organisation_form[email]' => 'test@orga.com',
            'registration_organisation_form[plainPassword]' => 'MotDePasse123!',
            'registration_organisation_form[passwordConfirmation]' => 'MotDePasse123!',
            'registration_organisation_form[agreeTerms]' => true,
        ]);

        // WHEN
        $client->submit($form);

        // THEN
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'test@orga.com']);
        $this->assertNotNull($user);
    }

    /**
     * @depends testSuccessfulRegistrationUser
     */
    public function testInvalidRegistration(): void
    {
        // GIVEN
        $client = static::createClient();

        // WHEN
        $crawler = $client->request('GET', '/inscription');

        // THEN
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Prêts à agir ?');

        // GIVEN
        $form = $crawler->selectButton('Inscription')->form([
            'registration_form[firstName]' => 'test',
            'registration_form[lastName]' => 'test',
            'registration_form[email]' => 'invalid-email',
            'registration_form[plainPassword]' => 'short',
            'registration_form[passwordConfirmation]' => 'short',
            'registration_form[agreeTerms]' => false,
        ]);

        // WHEN
        $client->submit($form);

        // THEN
        $this->assertSelectorTextContains('div', 'Veuillez entrer une adresse e-mail valide.');
        $this->assertSelectorTextContains('div', 'Votre mot de passe de avoir au moins 6 caractères.');
    }

    public function testLogin(): void
    {
        // GIVEN
        $client = static::createClient();

        // WHEN
        $crawler = $client->request('GET', '/login');

        // THEN
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Vous nous avez manqué !');

        // GIVEN
        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'test@example.com',
            'password' => 'MotDePasse123!',
        ]);

        // WHEN
        $client->submit($form);

        // THEN
        $this->assertResponseRedirects();
        $client->followRedirect();
    }
}