<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Tests\Functional\AuthenticatedWebTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use AuthenticatedWebTestCaseTrait;

    public function testLoginPageReturnsSuccessfulResponse(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    public function testLoginPageRootReturnsSuccessfulResponse(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    public function testLoginPageContainsForm(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testRegisterPageReturnsSuccessfulResponse(): void
    {
        $client = static::createClient();

        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }

    public function testRegisterPageContainsForm(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testAuthenticatedUserRedirectedFromLogin(): void
    {
        $client = static::createAuthenticatedClient();

        $client->request('GET', '/login');

        $this->assertResponseRedirects('/home');
    }

    public function testAuthenticatedUserRedirectedFromRegister(): void
    {
        $client = static::createAuthenticatedClient();

        $client->request('GET', '/register');

        $this->assertResponseRedirects('/home');
    }

    public function testLogoutRedirects(): void
    {
        $client = static::createAuthenticatedClient();

        $client->request('GET', '/logout');

        $this->assertResponseRedirects();
    }
}
