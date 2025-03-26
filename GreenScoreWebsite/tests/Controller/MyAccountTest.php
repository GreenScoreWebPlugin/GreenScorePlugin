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
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        self::$user = $userRepository->findOneByEmail('test@example.com');
        $client->loginUser(self::$user);
        $client->request('GET', '/mon-compte');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bonjour ' . self::$user->getFirstName());
    }

    /**
     * @depends testSuccessGetMyAccount
     */
    public function testSuccessGetMyAccountOrga()
    {
        $client = static::createClient();
        $client->loginUser(self::$user);
        $client->request('GET', '/mon-compte/organisation');

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
        $client = static::createClient();
        $client->loginUser(self::$user);
        $client->request(
            'POST',
            '/mon-compte/organisation/exist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['code' => 'TEST'])
        );

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
        $client = static::createClient();
        $client->loginUser(self::$user);
        $client->request(
            'POST',
            '/mon-compte/organisation/exist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['code' => 'TESTTEST'])
        );

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
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        self::$orga = $userRepository->findOneByEmail('test@orga.com');
        $client->loginUser(self::$orga);
        $client->request('GET', '/gerer-organisation');

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
        $client = static::createClient();
        $client->loginUser(self::$user);
        $client->request(
            'POST',
            '/mon-compte/organisation/exist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['code' => self::$orga->getIsAdminOf()->getOrganisationCode()])
        );

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
        $client = static::createClient();
        $client->loginUser(self::$user);
        $crawler = $client->request('POST', '/mon-compte/organisation');

        $form = $crawler->selectButton('Rejoindre')->form([
            'codeOrganisation' => self::$orga->getIsAdminOf()->getOrganisationCode(),
        ]);
        $client->submit($form);

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
        $client = static::createClient();
        $client->loginUser(self::$user);
        $client->request('POST', '/mon-compte/organisation/change-or-join-organisation', [
            'codeOrganisation' => 'DKIZ2F10'
        ]);

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
        $client = static::createClient();
        $client->loginUser(self::$user);
        $client->request('POST', '/mon-compte/organisation/remove-organisation');

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
        $client = static::createClient();
        $client->loginUser(self::$user);
        $client->request('DELETE', '/api/user/delete');

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
        $client = static::createClient();
        $client->loginUser(self::$orga);
        $client->request('DELETE', '/api/organization/delete');

        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => true, 'redirect' => '/inscription-organisation']),
            $client->getResponse()->getContent()
        );
    }
}