<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    public function testNeedAuth()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');
        $this->assertResponseRedirects('/login/azure');
    }
    
    public function testUserDenied()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user without group
        $userWithoutGroup = $userRepository->findOneBy(['displayName' => 'User Lambda']);

        // simulate $userWithoutGroup being logged in
        $client->loginUser($userWithoutGroup);

        $crawler = $client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdmin()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the admin users
        $adminUsers = $userRepository->getAdmins();

        // simulate $adminUsers[0] being logged in
        $client->loginUser($adminUsers[0]);

        $crawler = $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
    }
}
