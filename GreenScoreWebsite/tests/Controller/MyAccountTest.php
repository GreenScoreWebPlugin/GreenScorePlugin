<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MyAccountTest extends WebTestCase
{
    private static User $user;
    private static User $orga;

    /**
     * @depends App\Tests\Controller\RegistrationLoginTest::testSuccessfulRegistrationOrga
     */
    public function testSuccessGetMyAccount()
    {
        // GIVEN
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        // Retrieves the user created in the testSuccessfulRegistrationUser
        self::$user = $userRepository->findOneByEmail('test@example.com');
        $client->loginUser(self::$user);

        // WHEN
        $client->request('GET', '/mon-compte');

        // THEN
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bonjour ' . self::$user->getFirstName());
    }

    /**
     * @depends testSuccessGetMyAccount
     */
    public function testSuccessGetMyAccountOrga()
    {
        // GIVEN
        $client = static::createClient();
        $client->loginUser(self::$user);

        // WHEN
        $client->request('GET', '/mon-compte/organisation');

        // THEN
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'div.grid h1',
            'Vous n’avez pas encore rejoint d’organisation !'
        );
    }

    /**
     * @depends testSuccessGetMyAccountOrga
     */
    public function testFailOrgaExistBadLength()
    {
        // GIVEN
        $client = static::createClient();
        $client->loginUser(self::$user);

        // WHEN
        $client->request(
            'POST',
            '/mon-compte/organisation/exist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['code' => 'TEST'])
        );

        // THEN
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'errorMessage' => 'Code invalide']),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @depends testSuccessGetMyAccountOrga
     */
    public function testFailOrgaExistNotExist()
    {
        // GIVEN
        $client = static::createClient();
        $client->loginUser(self::$user);

        // WHEN
        $client->request(
            'POST',
            '/mon-compte/organisation/exist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['code' => 'TESTTEST'])
        );

        // THEN
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'errorMessage' => 'Ce code n\'est pas valide']),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @depends testSuccessGetMyAccount
     */
    public function testSuccessGetMyOrganisation()
    {
        // GIVEN
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        // Retrieves the organisation created in the testSuccessfulRegistrationOrga
        self::$orga = $userRepository->findOneByEmail('test@orga.com');
        $client->loginUser(self::$orga);

        // WHEN
        $client->request('GET', '/gerer-organisation');

        // THEN
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'h1',
            'Bonjour ' . self::$orga->getIsAdminOf()->getOrganisationName() . '!'
        );
    }

    /**
     * @depends testSuccessGetMyAccountOrga
     */
    public function testSuccessOrgaExist()
    {
        // GIVEN
        $client = static::createClient();
        $client->loginUser(self::$user);

        // WHEN
        $client->request(
            'POST',
            '/mon-compte/organisation/exist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['code' => self::$orga->getIsAdminOf()->getOrganisationCode()])
        );

        // THEN
        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => true]),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @depends testSuccessOrgaExist
     */
    public function testSuccessJoinOrga()
    {
        // GIVEN
        $client = static::createClient();
        $client->loginUser(self::$user);

        // WHEN
        $crawler = $client->request('POST', '/mon-compte/organisation');

        $form = $crawler->selectButton('Rejoindre')->form([
            'codeOrganisation' => self::$orga->getIsAdminOf()->getOrganisationCode(),
        ]);
        $client->submit($form);

        // THEN
        $this->assertResponseRedirects('/mon-compte/organisation');

        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'div.text-green-800 div span',
            'Vous avez bien rejoint l\'organisation nommée ' . self::$orga->getIsAdminOf()->getOrganisationName()
        );

    }

    /**
     * @depends testSuccessOrgaExist
     */
    public function testSuccessChangeOrga()
    {
        // GIVEN
        $client = static::createClient();
        $client->loginUser(self::$user);

        // WHEN
        $client->request('POST', '/mon-compte/organisation/change-or-join-organisation', [
            'codeOrganisation' => 'DKIZ2F10'
        ]);

        // THEN
        $this->assertResponseRedirects('/mon-compte/organisation');

        $client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            'div.text-green-800 div span',
            'Vous avez bien rejoint l\'organisation nommée leclerc'
        );

    }

    /**
     * @depends testSuccessChangeOrga
     */
    public function testSuccessLeaveOrga()
    {
        // GIVEN
        $client = static::createClient();
        $client->loginUser(self::$user);

        // WHEN
        $client->request('POST', '/mon-compte/organisation/remove-organisation');

        // THEN
        $this->assertResponseRedirects('/mon-compte/organisation');

        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'div.text-green-800 div span',
            'Vous avez quitté l\'organisation avec succès'
        );
    }

    /**
     * @depends testSuccessLeaveOrga
     */
    public function testSuccessDeleteUserAccount()
    {
        // GIVEN
        $client = static::createClient();
        $client->loginUser(self::$user);

        // WHEN
        $client->request('DELETE', '/api/user/delete');

        // THEN
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => true, 'redirect' => '/inscription']),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @depends testSuccessGetMyOrganisation
     */
    public function testSuccessDeleteOrgaAccount()
    {
        // GIVEN
        $client = static::createClient();
        $client->loginUser(self::$orga);

        // WHEN
        $client->request('DELETE', '/api/organization/delete');

        // THEN
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => true, 'redirect' => '/inscription-organisation']),
            $client->getResponse()->getContent()
        );
    }
}