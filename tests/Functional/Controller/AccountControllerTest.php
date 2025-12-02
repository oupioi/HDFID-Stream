<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AuthenticatedWebTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountControllerTest extends WebTestCase
{
    use AuthenticatedWebTestCaseTrait;

    public function testAccountPageRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/account');

        $this->assertResponseRedirects();
    }

    public function testAccountPageReturnsSuccessfulResponseForAuthenticatedUser(): void
    {
        $client = static::createAuthenticatedClient();

        $client->request('GET', '/account');

        $this->assertResponseIsSuccessful();
    }

    public function testAccountPageContainsForm(): void
    {
        $client = static::createAuthenticatedClient();

        $crawler = $client->request('GET', '/account');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }
}
