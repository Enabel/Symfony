<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testHomepage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome to SymfonyTemplate');
    }

    public function testSecureNeedAuth()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/secure');

        $this->assertResponseRedirects('/login/azure');
    }

    public function testSecureAsUser()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the admin users
        $adminUsers = $userRepository->getAdmins();

        // simulate $adminUsers[0] being logged in
        $client->loginUser($adminUsers[0]);

        $crawler = $client->request('GET', '/secure');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td.data-role', 'ROLE_ADMIN');
    }
}
