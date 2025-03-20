<?php

namespace App\Tests\Functionnal;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MyAccountTest extends RegistrationLoginTest
{
    private static User $user;

    public function testSuccessGetMyAccount()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        self::$user = $userRepository->findOneByEmail('test@test.fr');
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
        $this->assertSelectorTextContains('div.grid h1', 'Vous n’avez pas encore rejoint d’organisation !');
    }

    /**
     * @depends testSuccessGetMyAccountOrga
     */
    public function testSuccessOrgaExist()
    {
        $client = static::createClient();
        $client->loginUser(self::$user);
        $client->request('POST', '/mon-compte/organisation/exist', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['code' => 'N5DPL2MI']));

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => true]),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @depends testSuccessGetMyAccountOrga
     */
    public function testFailOrgaExistBadLength()
    {
        $client = static::createClient();
        $client->loginUser(self::$user);
        $client->request('POST', '/mon-compte/organisation/exist', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['code' => 'TEST']));

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
        $client->request('POST', '/mon-compte/organisation/exist', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['code' => 'TESTTEST']));

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'errorMessage' => 'Ce code n\'est pas valide']),
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

        // Soumettre un formulaire avec un email invalide
        $form = $crawler->selectButton('Rejoindre')->form([
            'codeOrganisation' => 'N5DPL2MI',
        ]);
        $client->submit($form);

        // Vérifier la redirection après inscription
        $this->assertResponseRedirects('/mon-compte/organisation');

        // Suivre la redirection
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.text-green-800 div span', 'Vous avez bien rejoint l\'organisation nommée orga pour les tests');

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

        // Vérifier la redirection après inscription
        $this->assertResponseRedirects('/mon-compte/organisation');

        // Suivre la redirection
        $client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('div.text-green-800 div span', 'Vous avez bien rejoint l\'organisation nommée leclerc');

    }

    /**
     * @depends testSuccessChangeOrga
     */
    public function testSuccessLeaveOrga()
    {
        $client = static::createClient();
        $client->loginUser(self::$user);
        $client->request('POST', '/mon-compte/organisation/remove-organisation');

        // Vérifier la redirection après inscription
        $this->assertResponseRedirects('/mon-compte/organisation');

        // Suivre la redirection
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.text-green-800 div span', 'Vous avez quitté l\'organisation avec succès');

    }
}